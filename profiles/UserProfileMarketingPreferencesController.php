<?php

namespace App\Http\Controllers;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidParameterException;
use App\Exceptions\ProfileNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Events\Publishes\ProfileUpdated;
use App\Http\Resources\UserProfileMarketingResource;
use App\Models\MarketingPreference;
use App\Models\UserMarketingPreference;
use App\Support\Encryption;
use App\Support\TSRGJWT;
use App\Support\UserProfileHelper;
use App\Support\UserProfileMarketingPreferencesHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserProfileMarketingPreferencesController extends ApiController
{
    /** PUT /profile/{userId}/MarketingPreferences API end point
     * Update a user profile
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function put(Request $request, TSRGJWT $jwtToken, int $userId)
    {
        // User not allowed to update data about another user
        if ($jwtToken->userId !== $userId) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }

        // Make sure we have a base profile to work with
        try {
            $profile = UserProfileHelper::getProfileByUserId($userId);
        } catch (ProfileNotFoundException $e) {
            // If the user doesn't have a profile, create one
            $profile = UserProfileHelper::createUserProfile($userId,[],[]);
        }

        foreach($request->data as $preference){
            // get the base preference, so we can establish the relationship
            UserProfileMarketingPreferencesHelper::updateOrCreateUserProfileMarketingPreference($userId, $preference);
        };

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }

        /**
     * GET /profile/{userId}/MarketingPreferences API end point
     * Update a user profile
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
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
        $preferences = UserProfileMarketingPreferencesHelper::getUserMarketingPreferences($userId);
        return (UserProfileMarketingResource::collection($preferences))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * PUT profile/marketing_preferences/unsubscribe API end point
     *
     * @param  Request  $request - The server request, including any parameters
     */
    public function unsubscribe(Request $request, TSRGJWT $jwtToken): JsonResponse
    {
        try {
            $request->validate([
                'data.user_id' => 'required|string',
                'data.marketing_preferences_code' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            throw new InvalidParameterException('user_id and marketing_preferences_code are required');
        }

        $encryptedUserId = $request->data['user_id'];
        // Decrypt userId
        $userId = Encryption::decryptSFMCString($encryptedUserId);
        $code = $request->data['marketing_preferences_code'];

        // If the user is logged in, make sure they are unsubscribing themselves
        if ($jwtToken->userId && (int)$jwtToken->userId !== (int)$userId) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }

        // Make sure we have a base profile to work with
        try {
            $profile = UserProfileHelper::getProfileByUserId($userId);
        } catch (ProfileNotFoundException $e) {
            // If the user doesn't have a profile, create one
            $profile = UserProfileHelper::createUserProfile($userId,[],[]);
        }

        // Perform the unsubscribe
        UserProfileMarketingPreferencesHelper::unsubscribe($userId, $code);

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        return $this->getSuccessfulResponse(204);
    }
}
