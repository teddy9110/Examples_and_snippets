<?php

namespace Rhf\Modules\Competition\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Competition\Filters\CompetitionEntryFilter;
use Rhf\Modules\Competition\Requests\EntryRequest;
use Rhf\Modules\Competition\Resources\CompetitionEntryResource;
use Rhf\Modules\Competition\Resources\EntryResource;
use Rhf\Modules\Competition\Services\CompetitionReportService;
use Rhf\Modules\Competition\Services\CompetitionService;
use Rhf\Modules\Competition\Services\CompetitionVotesService;
use Rhf\Modules\Competition\Services\EntryService;

class EntryController extends Controller
{
    private $entryService;
    private $competitionService;
    private $competitionReportService;
    private $competitionVotesService;

    public function __construct(
        CompetitionService $competitionService,
        EntryService $entryService,
        CompetitionReportService $competitionReportService,
        CompetitionVotesService $competitionVotesService
    ) {
        $this->competitionService = $competitionService;
        $this->entryService = $entryService;
        $this->competitionReportService = $competitionReportService;
        $this->competitionVotesService = $competitionVotesService;
    }

    /**
     * Return all Entries for a competition
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getEntries($id, Request $request)
    {
        $filters = $request->all();
        $filters['order']['sort_by'] = $request->input('sort_by', null);
        $filters['order']['sort_direction'] = $request->input('sort_direction', null);
        $pagination['page'] = intval($request->input('page', 1));
        $pagination['per_page'] = $request->input('limit', 20);

        $entries = $this->entryService->getEntries($id, new CompetitionEntryFilter($filters), $pagination);
        add_position($entries);

        return CompetitionEntryResource::collection($entries);
    }

    /**
     * return single entry for competition
     *
     * @param $entryId
     * @return CompetitionEntryResource
     */
    public function getEntry($entryId, Request $request)
    {
        $entry = $this->entryService->getEntry($entryId, ['competition']);
        return new CompetitionEntryResource($entry);
    }

    /**
     * Submit an entry for a competition
     * Checks if a user has an existing entry
     * @param $id
     * @param EntryRequest $request
     */
    public function submitEntry($id, EntryRequest $request)
    {
        try {
            $entryExists = $this->entryService->entryExists($id, Auth::id());
            if (!$entryExists) {
                $competition = $this->competitionService->getCompetition($id);
                if ($competition->active && ($competition->closed != true)) {
                    $data = $request->validated();
                    $data['competition_id'] = $id;
                    $data['title'] = $competition->title;
                    $data['user_id'] = auth('api')->user()->id;

                    $entry = $this->entryService->createEntry($id, $data, $request->file('image'), $competition);
                    return new EntryResource($entry);
                } else {
                    return response()->json([
                        'message' => 'Unable to submit an entry to an expired competition.'
                    ], 400);
                }
            }
            return response()->json([
                'message' => 'Unable to submit more than a single entry to the competition.'
            ], 400);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to submit an entry to an expired competition.');
        }
    }

    public function editEntry($id, EntryRequest $request)
    {
        $entry = $this->entryService->editEntry($id, $request->validated());
        return new EntryResource($entry);
    }

    /**
     * Checks if a user has votes
     * Add a vote to an entry
     * Log users vote in redis to prevent vote spamming
     *
     * @param $competition_id
     * @param $entry_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function entryVote($entry_id)
    {
        try {
            $entry = $this->entryService->getEntry($entry_id, ['competition']);
            if (!$entry->competition->closed) {
                if (!$this->hasUserVoted($entry_id)) {
                    $voted = $this->upvote($entry_id);
                    return response()->json([
                        'data' => [
                            'votes' => $voted->votes,
                            'voted' => $this->hasUserVoted($entry_id)
                        ]
                    ]);
                } else {
                    $voted = $this->downvote($entry_id);
                    return response()->json([
                        'data' => [
                            'votes' => $voted->votes,
                            'voted' => $this->hasUserVoted($entry_id)
                        ]
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Sorry, you are unable to vote on an expired competition or revoked entry'
                ], 400);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to vote on an expired competition or revoked entry.');
        }
    }

    /**
     * Report a users entry
     * if >= 10 soft delete
     *
     * @param $id
     */
    public function report($id, Request $request)
    {
        $validated = $request->validate([
            'report' => 'sometimes|string|max:255'
        ]);

        $entryReported = $this->competitionReportService->exists($id, Auth::id());
        if (!$entryReported) {
            $report = $request->input('report', 'This entry has been reported');
            $reported = $this->competitionReportService->createReport($id, $report);

            if ($reported) {
                $entry = $this->entryService->getEntry($id, ['competition']);
                $entry->increment('reports');

                if ($entry->reports === 10) {
                    $entry->suspended = true;
                    $entry->save();
                }
            }
            return response()->noContent();
        }
        return response()->json([
            'message' => 'Sorry, you can only report an entry once'
        ], 400);
    }

    /**
     * HARD Delete Entry
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteEntry($id)
    {
        $delete = $this->entryService->deleteEntry($id, false);
        return response()->noContent();
    }

    /**
     * Return all users entries
     *
     * @return mixed
     */
    public function userEntries()
    {
        return CompetitionEntryResource::collection($this->entryService->getUserEntries(auth('api')->user()->id));
    }

    /**
     * Get User entries for latest competition
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userCompetitionEntry()
    {
        $competition = $this->competitionService->getLatest();

        return EntryResource::collection(
            $this->entryService->getUserCompetitionEntries($competition->pluck('id'), auth('api')->user()->id)
        );
    }

    /**
     * Has user voted
     *
     * @param $entryId
     * @return mixed
     */
    public function hasUserVoted($entryId)
    {
        return $this->entryService->hasUserVoted($entryId);
    }

    /**
     * Upvote an entry
     *
     * @param $entryId
     * @return string
     */
    private function upvote($entryId)
    {
        $entry = $this->entryService->incrementVote($entryId);
        $this->competitionVotesService->castVote($entryId);
        return $entry;
    }

    /**
     * Delete a vote entry
     *
     * @param $entryId
     * @return string
     */
    private function downvote($entryId)
    {
        $entry = $this->entryService->decrementVote($entryId);
        $this->competitionVotesService->deleteVote($entryId);
        return $entry;
    }
}
