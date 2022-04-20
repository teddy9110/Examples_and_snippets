<?php

namespace Rhf\Modules\Competition\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Competition\Resources\CompetitionPreviousResource;
use Rhf\Modules\Competition\Resources\CompetitionResource;
use Rhf\Modules\Competition\Services\CompetitionService;

class CompetitionController extends Controller
{
    /**
     * @var CompetitionService
     */
    private $competitionService;

    public function __construct(CompetitionService $competitionService)
    {
        $this->competitionService = $competitionService;
    }

    /**
     * Return latest competition
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        if ($type === 'latest') {
            return CompetitionResource::collection($this->competitionService->getLatest());
        }

        if ($type === 'previous') {
            $pagination['page'] = intval($request->input('page', 1));
            $pagination['per_page'] = $request->input('limit', 20);
            return CompetitionPreviousResource::collection(
                $this->competitionService->getPrevious($pagination)
            );
        }
        return CompetitionResource::collection($this->competitionService->getAll());
    }

    /**
     * Return a competition by ID
     * @param $id
     * @return CompetitionResource
     */
    public function show($id): CompetitionResource
    {
        try {
            return new CompetitionResource($this->competitionService->getCompetition($id));
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Sorry, no competition with that id');
        }
    }
}
