<?php

namespace App\Http\Controllers;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidParameterException;
use App\Exceptions\ProfileNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Events\Publishes\ProfileUpdated;
use App\Http\Resources\UserProfileDataSharingPreferencesResource;
use App\Models\DataSharingPreference;
use App\Models\UserProfileDataSharingPreference;
use App\Support\Encryption;
use App\Support\TSRGJWT;
use App\Support\UserProfileHelper;
use App\Support\UserProfileDataSharingPreferencesHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserProfileDataSharingPreferencesController extends ApiController
{
    /**
     * GET /profile/{userId}/data_sharing_preferences API end point
     * 
     * Get the data sharing preferences for a given user
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * 
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function get(Request $request, TSRGJWT $jwtToken, int $userId): JsonResponse
    {
        // User not allowed to update data about another user
        if ($jwtToken->userId !== $userId) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }

        $preferences = UserProfileDataSharingPreferencesHelper::getUserDataSharingPreferences($userId);              

        return (new UserProfileDataSharingPreferencesResource($preferences))
            ->response()
            ->setStatusCode(200);
    }

    /** 
     * POST /profile/{userId}/data_sharing_preferences API end point
     * 
     * Update data sharing preferences for a given user
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * 
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function post(Request $request, TSRGJWT $jwtToken, int $userId)
    {
        // User not allowed to update data about another user
        if ($jwtToken->userId !== $userId) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }

        // Ensure that a questionCode has been provided
        try {
            $request->validate([
                'data.questionCode' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            throw new InvalidParameterException('questionCode is required');
        }
        
        $questionCode = isset($request->data['questionCode']) ? $request->data['questionCode'] : '';
        UserProfileDataSharingPreferencesHelper::share($userId, $questionCode);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }

    /** 
     * DELETE /profile/{userId}/data_sharing_preferences/{questionCode} API end point
     * 
     * Delete data sharing preferences for a given user
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * @param  string $questionCode - The unique identifier for the question code
     * 
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function delete(Request $request, TSRGJWT $jwtToken, int $userId, string $questionCode)
    {
        // User not allowed to update data about another user
        if ($jwtToken->userId !== $userId) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }
        
        UserProfileDataSharingPreferencesHelper::unshare($userId, $questionCode);
            
        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }
}
