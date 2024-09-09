<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidParameterException;
use App\Exceptions\ProfileNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Events\Publishes\ProfileUpdated;
use App\Support\TSRGJWT;
use App\Support\UserProfileHelper;
use App\Support\UserProfileInternationalHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileInternationalController extends ApiController
{

    /**
     * PATCH /internnational/{userId} API end point
     * Update a user profile's international answers
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function patch(Request $request, TSRGJWT $jwtToken, int $userId): JsonResponse
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

        $userProfileInternationalHelper = new UserProfileInternationalHelper();
        $userData = $request->json('data');

        if( empty($userData) || empty($userData['international_questions'])) {
            throw new InvalidParameterException ('Invalid order by parameter values');
        }

        $userProfileInternationalHelper->updateOrCreateInternationalUserData($userData, $userId);

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }
}
