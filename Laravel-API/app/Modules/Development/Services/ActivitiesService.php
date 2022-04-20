<?php

namespace Rhf\Modules\Development\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Activity\Services\ProgressService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\User\Services\TargetService;

class ActivitiesService
{
    protected $fullActivites = ['steps', 'calories', 'fiber', 'fat', 'protein', 'water', 'weight', 'carbohydrates'];

    protected $starActivites = ['step', 'calorie', 'fiber', 'protein', 'exercise', 'water', 'weight'];

    public function createUsersTestActivity(
        Carbon $startDate,
        Carbon $endDate,
        $user,
        $activityType = []
    ) {
        if (count($activityType) == 0) {
            $activityType = $this->fullActivites;
        }
        $targetService = new TargetService();
        $targetService->setUser($user);
        $goals = $targetService->getNutritionGoals();
        $period = new CarbonPeriod($startDate, '1 days', $endDate);
        $recordsCreated = [];
        foreach ($period as $date) {
            $activitesADay = rand(3, 7);
            for ($i = 0; $i <= $activitesADay; $i++) {
                $selectedActivity = $activityType[array_rand($activityType)];
                $min = $this->targetMin($selectedActivity, $goals);
                $max = $this->targetMax($selectedActivity, $goals);
                $recordsCreated[] = Activity::factory()
                    ->modifier($selectedActivity, $min, $max)
                    ->create([
                        'user_id' => $user->id,
                        'date' => $date,
                    ]);
            }
        }
        return $recordsCreated;
    }

    public function getStarsNeededForMedal($medal)
    {
        switch (ucfirst($medal)) {
            case 'Gold':
                return rand(6, 7);
            case 'Silver':
                return rand(4, 5);
            case 'Bronze':
                return rand(1, 3);
        }
    }

    public function generateStars(
        Carbon $date,
        User $user,
        int $starsNeeded
    ) {
        $activityType = $this->starActivites;
        $activity = [];
        for ($i = 0; $i <= $starsNeeded; $i++) {
            if (count($activityType) == 0) {
                $activityType = $this->starActivites;
            }
            $selectedActivity = array_rand(array_flip($activityType));
            $value = $this->getMedalValue($user->id, $selectedActivity, $date->copy());
            $activity[] =  $this->createMockActivity(
                $selectedActivity,
                $user->id,
                $value,
                Carbon::parse($date->copy())
            );
            if (($key = array_search($selectedActivity, $activityType)) !== false) {
                unset($activityType[$key]);
            }
        }
        return $activity;
    }

    public function createMockActivity(string $type, int $id, int $value, Carbon $date): Activity
    {
        $type = $this->transformType($type);
        return Activity::factory()
            ->create([
                'type' => $type,
                'user_id' => $id,
                'value' => $value,
                'date' => $date->format('Y-m-d H:m:i')
            ]);
    }

    public function getMedalValue(int $id, string $activity, $date)
    {
        $userPreferences = UserPreferences::where('user_id', $id)->first();

        switch ($activity) {
            case 'exercise':
                return 0;
            case 'step':
                $progressService = new ProgressService();
                $weeklyTarget = $progressService->getWeeklyTarget($date, $userPreferences->daily_step_goal);
                $target = $progressService->getAverageRemainingSteps(
                    $userPreferences->daily_step_goal,
                    $weeklyTarget,
                    $progressService->getWeeklyProgression('steps', $date)
                );
                return $target + ($target * 0.8);
            case 'weight':
                return $userPreferences->start_weight - rand(4, 10);
            case 'water':
                return $userPreferences->daily_water_goal + rand(2, 5);
            case 'calorie':
                return $userPreferences->daily_calorie_goal - rand(-40, 40);
            case 'fiber':
                return $userPreferences->daily_fiber_goal + rand(10, 30);
            case 'protein':
                return $userPreferences->daily_protein_goal + rand(10, 30);
        }
    }

    private function transformType(string $type): string
    {
        switch ($type) {
            case 'step':
                return 'steps';
            case 'calorie':
                return 'calories';
            default:
                return $type;
        }
    }

    private function targetMin(string $type, array $goals)
    {
        //'steps','calories','fiber','fat','protein','weight','carbohydrates'
        switch ($type) {
            case 'steps':
                return 5000;
            case 'weight':
                return 95;
            case 'water':
                return 1;
            case 'carbohydrates':
                return ($goals['carbohydrate'] - ($goals['carbohydrate'] * 0.8));
            case 'calories':
                return ($goals['calorie'] - ($goals['calorie'] * 0.8));
            case 'fiber':
            case 'fat':
            case 'protein':
                return ($goals[$type] - ($goals[$type] * 0.8));
        }
    }

    private function targetMax(string $type, array $goals)
    {
        switch ($type) {
            case 'steps':
                return 25000;
            case 'weight':
                return 495;
            case 'water':
                return 100;
            case 'carbohydrates':
                return ($goals['carbohydrate'] + ($goals['carbohydrate'] * 0.8));
            case 'calories':
                return ($goals['calorie'] - ($goals['calorie'] * 0.8));
            case 'fiber':
            case 'fat':
            case 'protein':
                return ($goals[$type] + ($goals[$type] * 0.8));
        }
    }
}
