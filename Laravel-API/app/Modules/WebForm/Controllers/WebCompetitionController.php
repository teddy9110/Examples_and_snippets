<?php

namespace Rhf\Modules\WebForm\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Competition\Models\Competition;
use Rhf\Modules\Competition\Models\CompetitionEntry;
use Rhf\Modules\Competition\Services\CompetitionService;
use Rhf\Modules\Competition\Services\EntryService;
use Rhf\Modules\WebForm\Resources\WebCompetitionResource;
use Rhf\Modules\WebForm\Resources\WebCompetitionsResource;
use Rhf\Modules\WebForm\Resources\WebCompetitionWinners;
use Rhf\Modules\WebForm\Resources\WebEntriesResource;
use Rhf\Modules\WebForm\Resources\WebEntryResource;

class WebCompetitionController extends Controller
{
    /**
     * @var CompetitionService
     */
    private $competitionService;
    /**
     * @var EntryService
     */
    private $entryService;

    public function __construct(CompetitionService $competitionService, EntryService $entryService)
    {
        $this->competitionService = $competitionService;
        $this->entryService = $entryService;
    }

    public function getAllCompetitions(Request $request)
    {
        $type = $request->input('type');
        if ($type == 'previous') {
            $previous = $this->competitionService->getAllPrevious();
            return WebCompetitionWinners::collection($previous);
        }
        $competitions = $this->competitionService->getAllWebsite();
        return WebCompetitionsResource::collection($competitions);
    }

    public function getCompetition($slug)
    {
        $competition = $this->competitionService->getBySlug($slug);
        $this->addPositionToCollection($competition);
        return new WebCompetitionResource($competition);
    }

    public function getCompetitionEntries($slug, Request $request)
    {
        $competition = Competition::with(['entries' => function ($q) use ($request) {
                return $q->limit($request->input('limit', 12))
                ->offset($request->input('offset', 0));
        }])
            ->where('slug', $slug)
            ->first();

        return new WebEntriesResource($competition);
    }

    public function getEntry($id)
    {
        try {
            $entry = CompetitionEntry::with(['competition'])
                ->where('id', $id)
                ->first();
            return new WebEntryResource($entry);
        } catch (Exception $e) {
            throw new Exception(
                'Sorry, unable to retrieve entry for competition'
            );
        }
    }

    public function getCompetitionLeaderboard($slug)
    {
        $competition = Competition::where('slug', $slug)->first();
        $this->addPositionToCollection($competition);
        return WebEntryResource::collection($competition->leaderboard);
    }

    public function entryVote($id, Request $request)
    {
        $ip = $request->input('ip');
        $canVote = $this->entryService->canVote($id, $ip);
        if ($canVote < config('app.competition_vote_limit')) {
            $entry = $this->entryService->incrementVote($id);
            $value = ['entry_id' => $entry->id, 'ip' => $ip, 'count' => $canVote + 1, 'votes' => $entry->votes];

            $this->entryService->castVote($id, $value);

            return response()->json([
                'data' => [
                    'votes' => $entry->votes
                ]
            ]);
        } else {
            return response()->json([
                'data' => [
                    'message' => 'Sorry, you are unable to vote on this competition'
                ]
            ], 400);
        }
    }

    public function removeEntryVote($id, Request $request)
    {
        $ip = $request->input('ip');
        $canVote = $this->entryService->canVote($id, $ip);
        if ($canVote != 0) {
            $entry = $this->entryService->decrementVote($id);
            $value = ['entry_id' => $entry->id, 'ip' => $ip, 'count' => $canVote - 1, 'votes' => $entry->votes];
            $this->entryService->castVote($id, $value);

            return response()->json([
                'data' => [
                    'votes' => $entry->votes
                ]
            ]);
        } else {
            return response()->json([
                'data' => [
                    'message' => 'Sorry, you have not voted in this competition'
                ]
            ], 400);
        }
    }

    /**
     * @param $competition
     */
    private function addPositionToCollection($competition)
    {
        return add_position($competition->leaderboard);
    }
}
