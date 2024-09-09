<?php

namespace App\Http\Controllers;

use App\Exceptions\ProfileNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InvalidParameterException;
use App\Events\Publishes\ProfileUpdated;
use App\Models\UserProfile;
use App\Models\UserProfileQualification;
use App\Support\TSRGJWT;
use App\Support\UserProfileQualificationsHelper;
use App\Support\UserProfileHelper;
use App\Jobs\SyncGraphJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserProfileQualificationsController extends ApiController
{
    /**
     * Deprecated as of 15/07/2024
     * PATCH /profile/{userId}/qualifications API end point
     * Update a user qualifications
     * This will only update existing qualifications
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function deprecatedPatch(Request $request, TSRGJWT $jwtToken, int $userId)
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

        foreach($request->data['qualification_ids'] as $qualification){
            // get the base preference, so we can establish the relationship
            UserProfileQualificationsHelper::updateUsersQualifications($qualification, $userId);
        };

        // Background job for syncing data into our graph
        SyncGraphJob::dispatch($profile);

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }


    /**
     * PUT /profile/{userId}/qualifications/{stage} API end point
     * Replace a users qualifications for a specific stage (current/future/previous)
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $userId - The unique identifier for the user
     * @param  string $stage - The stage of the qualification
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function putStage(Request $request, TSRGJWT $jwtToken, int $userId, string $stage)
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

        // Validate everything is in the request
        try {
            if (is_null($request->data['qualifications'])) {
                throw new ValidationException('missing qualifications array');
            }

            $request->validate([
                'data.qualifications' => 'array',
                'data.qualifications.*.qualification_id' => 'required|integer',
                'data.qualifications.*.start_year' => 'integer|nullable',
                'data.qualifications.*.end_year' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            throw new InvalidParameterException('Qualifications are missing required data');
        }

        $qualifications = $request->data['qualifications'] ?? [];
        UserProfileQualificationsHelper::replaceUsersQualificationsForStage($userId, $stage, $qualifications);

        // Temporarily sync the intended start year for university and postgraduate into profile table
        // TODO - Remove once onward processing is switched across
        if ($stage == 'future') {
            $profileUpdate = collect($qualifications)->filter(function($qualification){
                return in_array($qualification['qualification_id'], [UserProfileQualificationsHelper::UNDERGRADUATE_QUALIFICATION_ID, UserProfileQualificationsHelper::POSTGRADUATE_QUALIFICATION_ID]) && $qualification['start_year'];
            })->map(function($qualification){
                $update = [];
                if ($qualification['qualification_id'] === UserProfileQualificationsHelper::UNDERGRADUATE_QUALIFICATION_ID) {
                    $update['intended_university_start_year'] = $qualification['start_year'];
                } else {
                    $update['intended_postgraduate_start_year'] = $qualification['start_year'];
                }
                return $update;
            })->collapse()->toArray();

            if ($profileUpdate) {
                $profile->update($profileUpdate);
                $profile->save();
            }
        }

        // Background job for syncing data into our graph
        SyncGraphJob::dispatch($profile);

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }

    /**
     * PATCH /profile/qualifications/{id} API end point
     * Update a user qualifications
     *
     * @param  Request  $request - The server request, including any parameters
     * @param  TSRGJWT $jwtToken - The JWT Token object created by the middleware
     * @param  int $id - The unique identifier for the user
     * @return JsonResponse
     *
     * @throws \App\Exceptions\UnauthorizedException
     **/
    public function patch(Request $request, TSRGJWT $jwtToken, int $id)
    {
        $data = $request->data;

        // Make sure the user qualification exists
        $qualification = UserProfileQualificationsHelper::getQualificationById($id);

        // Make sure, only one qualification stage is set to true
        $stages = collect([
            $data['current'] ?? $qualification->current,
            $data['future'] ?? $qualification->future,
            $data['previous'] ?? $qualification->previous,
        ]);

        if ($stages->filter(function($stage) { return $stage == true; })->count() !== 1) {
            throw new InvalidParameterException('Only one qualification stage can be set to true');
        }

        // Check the qualification is owned by the requesting user
        if ($jwtToken->userId !== $qualification->user_id) {
            throw new UnauthorizedException('You do not have permission to perform this action.');
        }  

        // Update the qualification with the passed in data
        $qualification->update($data);
        $qualification->save();

        // Temporarily sync the intended start year for university and postgraduate into profile table
        // TODO - Remove once onward processing is switched across
        $profileUpdate = [];
        if ($qualification->future) {
            if ($qualification->qualification_id === UserProfileQualificationsHelper::UNDERGRADUATE_QUALIFICATION_ID) {
                $profileUpdate['intended_university_start_year'] = $qualification->start_year;
            } else {
                $profileUpdate['intended_postgraduate_start_year'] = $qualification->start_year;
            }
        }

        // Update the profile and retrieve it for sync jobs and events
        $profile = UserProfileHelper::updateOrCreateUserProfile($jwtToken->userId, $profileUpdate);

        // Background job for syncing data into our graph
        SyncGraphJob::dispatch($profile);

        // Publish the profile updated event
        ProfileUpdated::publish($profile);

        // Return nothing but the http status
        return $this->getSuccessfulResponse(204);
    }

}
