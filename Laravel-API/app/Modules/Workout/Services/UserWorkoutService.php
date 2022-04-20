<?php

namespace Rhf\Modules\Workout\Services;

use Rhf\Modules\User\Models\User;
use Rhf\Modules\Workout\Models\ExerciseLocation;
use Rhf\Modules\Workout\Models\Workout;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use RuntimeException;
use stdClass;
use Illuminate\Database\Eloquent\Collection;

class UserWorkoutService
{
    /**
     * @var User $user;
     */
    protected $user;

    public function __construct(User $user)
    {
        if (is_null($user)) {
            throw new RuntimeException('User must be an instance of User when creating a new UserWorkoutService');
        }
        $this->user = $user;
    }

    public function getUserWorkouts(): Collection
    {
        $query = Workout::query();

        $location = ExerciseLocation::find($this->getExerciseLocation());

        $workoutsV3Available = workouts_v3_available();

        $query
            ->where('workout_flow', $workoutsV3Available ? Workout::FLOW_YOUTUBE : Workout::FLOW_STANDARD)
            ->where('exercise_location_id', $location->id ?? null)
            ->where('exercise_frequency_id', $this->getExerciseFrequency());

        if (!$workoutsV3Available) {
            // Ignore exercise level for Gym based workouts
            if ($location && $location->slug === ExerciseLocation::SLUG_HOME) {
                $query = $query->where('exercise_level_id', $this->getExerciseLevel());
            }
        }

        $workouts = $query->orderBy('order')->get();
        $workouts = $this->padWithRestDays($workouts);

        return $workouts;
    }

    public function orderWorkoutsBySchedule(Collection $workouts, array $schedule = null): Collection
    {
        $schedule = $schedule ?? $this->getFlippedSchedule();

        if (is_null($schedule)) {
            return $workouts;
        }
        $scheduledDayCount = count($schedule);
        return $workouts->take($scheduledDayCount)->sortBy(function ($item, $key) use ($schedule) {
            return $schedule[$key + 1];
        })->values();
    }

    public function retrieveWorkoutSchedule(): array
    {
        $schedule = $this->getFlippedSchedule();
        $workouts = $this->orderWorkoutsBySchedule($this->getUserWorkouts(), $schedule)->toArray();
        if (is_null($schedule)) {
            $schedule = array_flip($this->getDefaultSchedule(count($workouts)));
        }
        $scheduledWorkouts = [];
        foreach ($schedule as $workoutDay => $key) {
            $day = new stdClass();
            $day->workout_day = $workoutDay;
            $day->workout_name = $workouts[$key]['title'];
            if (array_key_exists('type', $workouts[$key])) {
                $day->workout_id = -1;
            } else {
                $day->workout_id = $workouts[$key]['id'];
            }
            if ($key > 6) {
                break;
            }
            $scheduledWorkouts[] = $day;
        }
        return $scheduledWorkouts;
    }

    public function saveWorkoutSchedule($schedule): WorkoutPreference
    {
        return WorkoutPreference::updateOrCreate(
            [
                'user_id' => $this->user->id,
            ],
            [
                'user_id' => $this->user->id,
                'schedule' => is_null($schedule) ? null : json_encode($schedule)
            ]
        );
    }

    private function getExerciseLevel(): ?int
    {
        return $this->user->getPreference('exercise_level_id');
    }

    private function getExerciseLocation(): ?int
    {
        return $this->user->getPreference('exercise_location_id');
    }

    private function getExerciseFrequency(): ?int
    {
        return $this->user->getPreference('exercise_frequency_id');
    }

    private function padWithRestDays(Collection $workouts): Collection
    {
        $restDays = 7 - $workouts->count();
        $restIndexes = $this->getRestIndexes($restDays);
        foreach ($restIndexes as $index) {
            $workouts->splice($index, 0, [$this->generateRestWorkout($index + 1)]);
        }
        return $workouts;
    }

    private function generateRestWorkout(int $order = null): Workout
    {
        $restWorkout = new Workout();
        $restWorkout->title = 'Rest';
        $restWorkout->type = Workout::TYPE_REST;
        if (!is_null($order)) {
            $restWorkout->order = $order;
        }
        return $restWorkout;
    }

    private function getDefaultSchedule(int $workoutCount = 0): array
    {
        $schedule = [];
        $i = 1;
        while ($i < $workoutCount + 1) {
            $schedule[] = $i;
            $i++;
        }
        return $schedule;
    }

    private function getFlippedSchedule(): ?array
    {
        $workoutPreferences = WorkoutPreference::where('user_id', $this->user->id)->first();
        $schedule = json_decode($workoutPreferences->schedule ?? null, true);
        if (!is_array($schedule) || count($schedule) != 7) {
            return null;
        }
        return array_flip($schedule);
    }

    private function getRestIndexes($restDayCount): array
    {
        switch ($restDayCount) {
            case 1:
                return [6];
            case 2:
                return [4, 6];
            case 3:
                return [1, 3, 5];
            case 4:
                return [1, 3, 5, 6];
            case 5:
                return [1, 2, 4, 5, 6];
            case 6:
                return [1, 2, 3, 4, 5, 6];
            case 7:
                return [0, 1, 2, 3, 4, 5, 6];
            default:
                return [];
        }
    }
}
