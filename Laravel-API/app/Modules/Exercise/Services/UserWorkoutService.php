<?php

namespace Rhf\Modules\Exercise\Services;

use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Content\Services\FacebookContentService;
use Rhf\Modules\Exercise\Models\Exercise;

class UserWorkoutService
{
    protected $workoutCount = 7; // Workouts will be padded with rest days up to this number
    protected $user; // The user we are retrieving for
    protected $workouts; // The collection of workouts that will be returned

    // Set the schedule of rest days versus workouts
    protected $schedule = [
        222 => [2,1,2,1,2,1,1],
        333 => [3,1,3,1,3,1,1],
        445566 => [4,5,6,4,5,6,1],
        778899 => [7,8,9,7,8,9,1],
        101010 => [10,1,10,1,10,1,1],
        111111 => [11,1,11,1,11,1,1],
        121213131414 => [12,13,14,12,13,14,1],
        151516161717 => [15,16,17,15,16,17,1],
    ];

    /**
     * Create a new TargetService instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->setUser($user);
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**************************************************
    *
    * CALCULATORS
    *
    ***************************************************/

    /**
     * Calculte the available workouts for the given user.
     *
     * @return self
     * @throws \Exception
     */
    public function calculateWorkouts()
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new FitnessBadRequestException(
                'Error, unable to calculate available workouts. Please contact Team RH support.'
            );
        }

        // Filters for workouts
        $exerciseLevelId = $this->getExerciseLevel();
        $exerciseFrequencyId = $this->getExerciseFrequency();
        $exerciseLocationId = $this->getExerciseLocation();

        // Retrieve the static rest workout
        $restWorkout = ExerciseCategory::where('title', '=', 'Rest')->first();

        // Retrieve the exercise categories (workouts)
        $workouts = ExerciseFrequency::where('id', '=', $exerciseFrequencyId)->first()
            ->exerciseCategories()
            ->where('exercise_location_id', '=', $exerciseLocationId)
            ->where('exercise_level_id', '=', $exerciseLevelId)
            ->get();

        // Get the schedule key so we can work out sort order
        $scheduleKey = implode('', $workouts->pluck('id')->toArray());
        if (isset($this->schedule[$scheduleKey])) {
            $workouts = $workouts->toArray();

            // Append the rest workout
            $idIndexedWorkouts = [];
            $idIndexedWorkouts[$restWorkout->id] = $restWorkout;
            // Loop the workouts and index them by ID in an array so they can be ordered as per schedule
            foreach ($workouts as $workout) {
                $idIndexedWorkouts[$workout['id']] = $workout;
            }

            $workouts = [];
            foreach ($this->schedule[$scheduleKey] as $schedule) {
                $workouts[] = $idIndexedWorkouts[$schedule];
            }
        } else {
            $workouts = $workouts->toArray();

            // Add required remaining rest days
            $restDayCount = $this->workoutCount - count($workouts);
            if ($restDayCount > 0) {
                for ($i = $restDayCount; $i > 0; $i--) {
                    $workouts[] = $restWorkout;
                }
            }
        }

        // Attach the videos
        foreach ($workouts as $key => $workout) {
            // If there is a content_video value on the workout, generate an S3 URL
            if ($workout['content_video']) {
                $exerciseContentVideoFileService = new ExerciseVideoFileService();
                $workouts[$key]['video'] = $exerciseContentVideoFileService->getPublicUrl($workout);
            } elseif ($workout['facebook_id']) {
                // Create the facebook content service
                $facebookContentService = new FacebookContentService();

                if ($workout['facebook_id']) {
                    $post = $facebookContentService->videoById($workout['facebook_id']);
                    $workouts[$key]['video'] = $post['source'];
                }
            }
        }

        return $workouts;
    }


    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the user's exercise level.
     *
     * @return void
     */
    public function getExerciseLevel()
    {
        if (!$this->getUser()->hasPreference('exercise_level_id')) {
            throw new FitnessPreconditionException('Error, no exercise level set. Please contact Team RH Support.');
        }

        return $this->getUser()->getPreference('exercise_level_id');
    }

    /**
     * Return the user's exercise frequency.
     *
     * @return void
     */
    public function getExerciseFrequency()
    {
        if (!$this->getUser()->hasPreference('exercise_frequency_id')) {
            throw new FitnessPreconditionException('Error, no exercise frequency set. Please contact Team RH Support.');
        }
        return $this->getUser()->getPreference('exercise_frequency_id');
    }

    /**
     * Return the user's exercise location.
     *
     * @return void
     */
    public function getExerciseLocation()
    {
        if (!$this->getUser()->hasPreference('exercise_location_id')) {
            throw new FitnessPreconditionException('Error, no exercise location set. Please contact Team RH Support.');
        }
        return $this->getUser()->getPreference('exercise_location_id');
    }

    /**
     * Return the user associated to the instance of the service.
     *
     * @return \Rhf\Modules\User\Models\User
     */
    public function getUser()
    {
        return isset($this->user) ? $this->user : null;
    }

    /**
     * Return the workout with associated facebook source url.
     */
    public function getWorkout($id)
    {
        $exercises = Exercise::byCategory($id)->orderBy('sort_order', 'ASC')->get();

        foreach ($exercises as $exercise) {
            if ($exercise->content_video) {
                $exerciseContentVideoFileService = new ExerciseVideoFileService();
                $exercise->video = $exerciseContentVideoFileService->getPublicUrl($exercise);
            } elseif ($exercise->video) {
                // Check if there is a Facebook ID on the exercise
                // Create the facebook content service
                $facebookContentService = new FacebookContentService();
                $post = $facebookContentService->videoById($exercise->video);
                $exercise->video = $post['source'];
                $exercise->facebook_id = $post['id'];
            }
        }

        return $exercises;
    }

    /**
     * Retrieve the workouts.
     *
     * @return array
     * @throws \Exception
     */
    private function getWorkouts()
    {
        if (!isset($this->workouts)) {
            throw new FitnessPreconditionException(
                'Error, unable to retrieve workouts. Please contact Team RH Support.'
            );
        }
        return $this->workouts;
    }


    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/

    /**
     * Set the user associated to the instance of the service.
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
