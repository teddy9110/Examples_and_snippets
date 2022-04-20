<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\User\Models\UserProgress;
use Rhf\Modules\User\Services\TargetService;
use Rhf\Modules\User\Services\UserFileService;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use Rhf\Modules\Workout\Services\UserWorkoutService;

class AdminUserDetailedResource extends JsonResource
{
    protected function createProgressItem(UserProgress $progress)
    {
        $userFileService = app(UserFileService::class);
        $picture = $progress
            ->progressPicture()
            ->orderByRaw('FIELD(type, "front", "side")')
            ->first();

        return [
            'weight' => floatval($progress->weight_value),
            'date' => $progress->updated_at->format('d/m/Y'),
            'picture_uri' => is_null($picture) ? null : $userFileService->getPublicUrl($picture)
        ];
    }

    protected function getWorkoutPreferences()
    {
        $service = new UserWorkoutService($this->resource);
        $workoutPreferences = $this->workoutPreferences;
        if (is_null($workoutPreferences)) {
            $workoutPreferences = new WorkoutPreference();
        }
        $workoutPreferences->schedule = $service->retrieveWorkoutSchedule();
        return $workoutPreferences;
    }

    protected function createProgress()
    {
        $progressQuery = $this
            ->progress()
            ->orderBy('updated_at', 'desc')
            ->take(2)
            ->get();

        $progressData = null;
        if (count($progressQuery) == 2) {
            $from = $this->createProgressItem($progressQuery[1]);
            $to = $this->createProgressItem($progressQuery[0]);

            if ($from['picture_uri'] != null && $to['picture_uri'] != null) {
                $progressData = [
                    'weight_change' => $progressQuery[0]->weight_value - $progressQuery[1]->weight_value,
                    'from' => $from,
                    'to' => $to
                ];
            }
        }

        return $progressData;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $preferences = $this->preferences;
        $subscriptions = $this->subscription;
        $progress = $this->progress()->first();
        $this->targetService = new TargetService();
        $this->targetService->setUser($this->resource);
        $weightActivity = $this
            ->activity()
            ->where('type', 'weight')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!is_null($weightActivity)) {
            $currentWeight = $weightActivity->value;
        } else {
            $currentWeight = isset($progress) ? $progress->weight_value : $preferences->start_weight;
        }

        $staffNotes = $this
            ->staffNotes()
            ->take(5)
            ->orderBy('updated_at', 'desc')
            ->get();

        $frequency = $this->getExerciseFrequencyAttribute();
        $location = $this->getExerciseLocationAttribute();
        $level = $this->getExerciseLevelAttribute();

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->surname,
            'email' => $this->email,
            'has_paid' => $this->paid == 1,
            'has_mfp' => $this->hasConnectedMfp(),
            'is_active' => $this->active == 1,
            'progress_picture_consent' => $preferences->progress_picture_consent,
            'dob' => $preferences->dob,
            'gender' => $preferences->gender,
            'staff_user' => $this->staff_user,
            'medical_conditions' => $preferences->medical_conditions,
            'personal_goals' => $preferences->personal_goals,
            'start_height' => $preferences->start_height,
            'start_weight' => $preferences->start_weight,
            'current_weight' => floatval(number_format($currentWeight, 2, '.', '')),
            'weekly_workouts' => isset($frequency) ? $frequency->amount : 0,
            'workout_location' => isset($location) ? $location->title : 0,
            'workout_level' => isset($level) ? $level->title : 0,
            'expires_at' => isset($this->expiry_date) ? $this->expiry_date->format('d/m/Y') : null,
            'next_payment_date' => isset($this->next_payment_date) ? $this->next_payment_date->format('d/m/Y') : null,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->created_at->format('d/m/Y'),
            'custom_goals' => $this->targetService->getCustomGoals(),
            'goals' => [
                'step' => $preferences->daily_step_goal,
                'calorie' => $preferences->daily_calorie_goal,
                'water' => $preferences->daily_water_goal,
                'protein' => $preferences->daily_protein_goal,
                'carbohydrate' => $preferences->daily_carbohydrate_goal,
                'fat' => $preferences->daily_fat_goal,
                'fiber' => $preferences->daily_fiber_goal,
            ],
            'workout_preferences' => new AdminUserWorkoutPreferencesResource($this->getWorkoutPreferences()),
            'exercise_location' => new AdminExerciseLocationResource($location),
            'exercise_level' => new AdminExerciseLevelResource($level),
            'exercise_frequency' => new AdminExerciseFrequencyResource($frequency),
            'staff_notes' => AdminStaffNoteResource::collection($staffNotes),
            'progress' => $this->createProgress(),
            'role' => new AdminUserPermissionsResource($this->role),
            'subscription' => [
                'provider' => isset($subscriptions->subscription_provider) ? $subscriptions->subscription_provider : '',
                'frequency' => isset($subscriptions->subscription_frequency) ?
                    ($subscriptions->subscription_frequency == 'annual' ? 'annual' : 'direct debit') :
                    '',
                'plan' => isset($subscriptions->subscription_plan) ? $subscriptions->subscription_plan : ''
            ],
            'subscription_data' => $this->subscription_data,
            'weight_preference' => $preferences->weight_unit,
            'weekly_activity_log_count' => $this->weekActivityLog()->count(),
            'period_tracker' => $preferences->period_tracker
        ];
    }
}
