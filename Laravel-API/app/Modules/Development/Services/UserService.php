<?php

namespace Rhf\Modules\Development\Services;

use Carbon\Carbon;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use Illuminate\Support\Facades\Hash;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\User\Services\TargetService;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Models\ExerciseLocation;

class UserService
{
    public function createUser(string $requestedPassword, $role, $paid): User
    {
        $user =  User::factory()->create([
            'password' => Hash::make($requestedPassword),
            'active' => false,
            'paid' => $paid,
            'role_id' => $role,
            'test_user' => true,
            'expiry_date' => Carbon::now()->addYear()->toDateTimeString(),
        ]);
        return $user;
    }

    public function seedUserPreferences(User $user)
    {
        $exerciseFrequencyId = array_rand(array_flip(ExerciseFrequency::pluck('id')->toArray()));
        $exerciseLocationId = array_rand(array_flip(ExerciseLocation::pluck('id')->toArray()));
        $exerciseLevelId = null;

        UserPreferences::factory()->create(
            [
                'user_id' => $user->id,
                'user_role' => $user->role_id,
                'gender' => array_rand(array_flip(['Male', 'Female'])),
                'exercise_frequency_id' => $exerciseFrequencyId,
                'exercise_location_id' => $exerciseLocationId,
                'exercise_level_id' => $exerciseLevelId,
            ]
        );
        $workoutPreferences = $this->seedUserWorkoutPreferences(
            $user,
            $exerciseFrequencyId,
            $exerciseLocationId,
            $exerciseLevelId
        );
        $this->recalculateUserGoals($user);
    }

    public function migrateWorkoutPreferences(User $user, int $frequencyId, int $locationId)
    {
        WorkoutPreference::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'user_id' => $user->id,
                'schedule' => null,
                'exercise_frequency_id' => $frequencyId,
                'exercise_level_id' => null,
                'exercise_location_id' => $locationId,
            ]
        );
    }

    public function deleteUser($userIds)
    {
        $users = User::whereIn('id', $userIds)->where('test_user', true)->get();
        foreach ($users as $user) {
            $user->delete();
        }
    }

    private function seedUserWorkoutPreferences(
        User $user,
        $exerciseFrequencyId,
        $exerciseLocationId,
        $exerciseLevelId
    ) {
        return WorkoutPreference::factory()->create([
            'user_id' => $user->id,
            'exercise_frequency_id' => $exerciseFrequencyId,
            'exercise_location_id' => $exerciseLocationId,
            'exercise_level_id' => $exerciseLevelId,
        ]);
    }

    private function recalculateUserGoals(User $user)
    {
        $user = $user->fresh();
        $targetService = new TargetService();
        $targetService->setUser($user);
        $calories = $targetService->calculateCalorieGoal(
            $user->getPreference('start_weight'),
            $user->getPreference('daily_step_goal'),
            ExerciseLocation::where('id', $user->getPreference('exercise_location_id'))->first(),
            ExerciseFrequency::where('id', $user->getPreference('exercise_frequency_id'))->first()
        );

        $nutritionGoals = $targetService->getGoalsArray();

        foreach ($nutritionGoals as $type) {
            if ($type != 'calorie') {
                $baselineNutrition[$type] = (int) $targetService->calculateNutritionGoal($type, $calories);
            }
        }

        UserPreferences::where('user_id', $user->id)->update([
            'daily_calorie_goal' => $calories,
            'daily_protein_goal' => $baselineNutrition['protein'],
            'daily_carbohydrate_goal' => $baselineNutrition['carbohydrate'],
            'daily_fat_goal' => $baselineNutrition['fat'],
            'daily_fiber_goal' => $baselineNutrition['fiber'],
        ]);
    }
}
