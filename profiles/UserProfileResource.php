<?php

namespace App\Http\Resources;

use App\Http\Resources\UserProfileLearningProviderResource;
use App\Http\Resources\UserProfileInternationalApplicationResource;
use App\Http\Resources\UserProfileQualificationResource;
use App\Http\Resources\UserProfileTopicsOfInterestResource;
use App\Http\Resources\MarketingPreferenceResource;
use App\Models\MarketingPreference;
use App\Support\UserProfileHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $currentAcademicYear = UserProfileHelper::getCurrentAcademicYear();

        return ['user_profile' => [
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile' => $this->mobile,
            'post_code' => $this->post_code,
            'city' => $this->city,
            'country' => $this->country,
            'gender' => $this->gender,
            'year_group' => $this->year_group,
            'career_phase' => $this->career_phase,
            'current_qualifications' => UserProfileQualificationResource::collection($this->qualifications->where('current', 1)),
            'future_qualifications' => UserProfileQualificationResource::collection($this->qualifications->where('future', 1)),
            'current_subjects' => $this->subjects->where('current', 1)->map(fn ($subject) => $subject->subject_id)->values(),
            'future_subjects' => $this->subjects->where('future', 1)->map(fn ($subject) => $subject->subject_id)->values(),
            'current_learning_providers' => $this->learning_providers->where('current', 1)->map(fn ($learningProvider) => $learningProvider->learning_provider_id)->values(),
            'future_learning_providers' => $this->learning_providers->where('future', 1)->map(fn ($learningProvider) => $learningProvider->learning_provider_id)->values(),
            'international_application' => new UserProfileInternationalApplicationResource($this->internationalApplication),
            'last_updated' => $this->updated_at,
            'questions_answered' => $this->QuestionsAnswered,

            // START Deprecated - remove these once front-end is updated

            // These four are all covered by `current_qualifications` and `future_qualifications`
            'intended_university_start_year' => $this->intended_university_start_year,
            'intended_postgraduate_start_year' => $this->intended_postgraduate_start_year,
            'qualifications' => array_values($this->qualifications->filter(fn($qualification) => $qualification['end_year'] >= $currentAcademicYear || !$qualification['end_year'])->map(fn ($qualification) => $qualification->qualification_id)->toArray()),
            'end_year' => $this->qualifications->map(function($qualification) {
                    return [
                        'id' => $qualification['qualification_id'],
                        'end_year' => $qualification['end_year']
                    ];
                })->toArray(),

            // These two are marketing preferences now
            'clearing_opt_in' => null,
            'student_loans' => $this->marketing_preferences->where('marketing_preference.code','B2B2C_STUDENT_LOANS')->first()->frequency ?? null,

            // END Deprecated

            'email_marketing_preferences' => $this->getEmailMarketingPreferences(),
            'topics_of_interest' => UserProfileTopicsOfInterestResource::collection($this->topicsOfInterest),

            'email_opted_out_at' => $this->email_opted_out_at,
            'sms_opted_out_at' => $this->sms_opted_out_at,
        ]];
    }
}
