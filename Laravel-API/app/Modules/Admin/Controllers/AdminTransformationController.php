<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Resources\AdminTransformationStoriesResource;
use Rhf\Modules\WebForm\Services\TransformationService;

class AdminTransformationController extends Controller
{
    /**
     * @var TransformationService
     */
    private $transformationService;

    /**
     * WebFormController constructor.
     * @param TransformationService $transformationService
     */
    public function __construct(TransformationService $transformationService)
    {
        $this->transformationService = $transformationService;
    }

    /**
     * Get All stories
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getStories(Request $request)
    {
        try {
            $page = intval($request->input('page', 1));
            $transformations = $this->transformationService->getAll($page);
            return AdminTransformationStoriesResource::collection($transformations);
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, unable to retrieve user stories',
                $e->getCode()
            );
        }
    }

    /**
     * @param $id
     * @return AdminTransformationStoriesResource
     */
    public function getStory($id): AdminTransformationStoriesResource
    {
        try {
            $transformations = $this->transformationService->getTransformation($id);
            return new AdminTransformationStoriesResource($transformations);
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, unable to retrieve user story',
                $e->getCode()
            );
        }
    }

    /**
     * Delete user story
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStory($id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->transformationService->deleteTransformation($id);
            return response()->json([
                'status' => 'successful',
                'message' => 'User Transformation has been successfully deleted'
            ]);
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, this item was not found, please try again.',
                $e->getCode()
            );
        }
    }
}
