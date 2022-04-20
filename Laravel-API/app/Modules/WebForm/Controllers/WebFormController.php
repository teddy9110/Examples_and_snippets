<?php

namespace Rhf\Modules\WebForm\Controllers;

use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\WebForm\Requests\TransformationRequest;
use Rhf\Modules\WebForm\Services\TransformationService;

class WebFormController extends Controller
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
     * @param TransformationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stories(TransformationRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $story = $this->transformationService->createTransformation(
                $request->validated(),
                $request->file('before_photo'),
                $request->file('after_photo')
            );
            return response()->json([
                'status' => 'success',
                'message' => 'User story successfully created'
            ]);
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, unable to create your user story. Please try re-submitting your story',
                $e->getCode()
            );
        }
    }
}
