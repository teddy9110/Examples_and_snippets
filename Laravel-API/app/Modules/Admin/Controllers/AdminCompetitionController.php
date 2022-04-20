<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminCompetitionRequest;
use Rhf\Modules\Admin\Resources\AdminCompetitionsResource;
use Rhf\Modules\Admin\Resources\AdminEntryResource;
use Rhf\Modules\Competition\Filters\CompetitionEntryFilter;
use Rhf\Modules\Competition\Filters\CompetitionFilter;
use Rhf\Modules\Competition\Models\CompetitionWinners;
use Rhf\Modules\Competition\Services\CompetitionService;
use Rhf\Modules\Competition\Services\EntryService;

class AdminCompetitionController extends Controller
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

    public function index(Request $request)
    {
        $filters = $request->all();
        $filters['order']['sort_by'] = $request->input('sort_by', 'start_date');
        $filters['order']['sort_direction'] = $request->input('sort_direction', 'desc');

        $pagination['page'] = intval($request->input('page', 1));
        $pagination['per_page'] = $request->input('limit', 20);

        $competitions = $this->competitionService->paginate(new CompetitionFilter($filters), $pagination);
        return AdminCompetitionsResource::collection($competitions);
    }

    public function create(AdminCompetitionRequest $request)
    {
        $create = $this->competitionService->createCompetition(
            $request->validated(),
            $request->file('desktop_image'),
            $request->file('mobile_image'),
            $request->file('app_image')
        );

        return new AdminCompetitionsResource($create);
    }

    public function show($id)
    {
        return new AdminCompetitionsResource($this->competitionService->getCompetition($id));
    }

    public function editCompetition(AdminCompetitionRequest $request, $id)
    {
        $comp = $this->competitionService->updateCompetition($id, $request->validated());
        return response()->json([
            'message' => 'Competition Successfully Updated'
        ]);
    }

    public function editImage(Request $request, $id)
    {
        $array = explode('/', $request->getPathInfo());
        $type = end($array);
        $image = $request->file($type . '_image', null);
        $this->competitionService->updateImage($id, $image, $type);
        return response()->json([
            'message' => 'Competition Image successfully updated'
        ], 200);
    }

    public function deleteCompetition($id)
    {
        $this->competitionService->deleteCompetition($id);
        return response()->noContent();
    }

    public function restoreCompetition($id)
    {
        $this->competitionService->restoreCompetition($id);
        return response()->noContent();
    }

    public function getCompetitionEntries($id, Request $request)
    {
        $filters = $request->all();
        $filters['order']['sort_by'] = $request->input('sort_by', null);
        $filters['order']['sort_direction'] = $request->input('sort_direction', null);

        $pagination['page'] = intval($request->input('page', 1));
        $pagination['per_page'] = $request->input('limit', 20);
        $entries = $this->entryService->getAdminEntries($id, new CompetitionEntryFilter($filters), $pagination);
        return AdminEntryResource::collection($entries);
    }

    public function showEntry($id)
    {
        return new AdminEntryResource($this->entryService->getEntry($id, ['competition', 'reportDetails']));
    }

    public function suspendEntry($id)
    {
        $this->entryService->suspendEntry($id);
        return response()->json([
            'message' => 'Entry Suspended'
        ]);
    }

    public function unsuspendEntry($id)
    {
        $entry = $this->entryService->unsuspendEntry($id);
        return response()->json([
            'message' => 'Entry Unsuspended'
        ]);
    }

    public function restoreEntry($id)
    {
        $entry = $this->entryService->restoreEntry($id);
        return response()->json([
            'message' => 'Entry Restored'
        ]);
    }

    public function deleteEntry($id)
    {
        $this->entryService->deleteEntry($id, false);
        return response()->noContent();
    }

    /**
     * Select as winner
     *
     * @param $id
     */
    public function markAsWinner($id)
    {
        $entry = $this->entryService->getEntry($id, ['competition']);
        $competitionWinner = new CompetitionWinners();

        $hasWinner = $competitionWinner->where('entry_id', $id)
            ->where('competition_id', $entry->competition_id)
            ->exists();

        if (!$hasWinner) {
            $competitionWinner->create([
                 'entry_id' => $id,
                 'competition_id' => $entry->competition_id
            ]);
            return response()->json([
                'message' => 'The winning entry was by ' . $entry->user->name
            ]);
        }
        return response()->json([
            'message' => 'Winner already selected, you cannot have multiple winners'
        ]);
    }
}
