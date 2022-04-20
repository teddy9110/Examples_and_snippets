<?php

namespace Rhf\Console\Commands\Workouts;

use Illuminate\Console\Command;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\Workout\Models\WorkoutPreference;

class PreferencesMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workouts:migrate-user-preferences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if a user exists and migrates a users workout preferences
        from `user preferences` to `workout preferences`. If the user does not exist create.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $chunkSize = 30000;
        $count = UserPreferences::count();
        $totalChunks = ceil($count / $chunkSize);

        $this->line('Total users to migrate: ' . $count);

        for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
            $from = $chunk * $chunkSize;
            $to = $from + $chunkSize;
            $this->line('Migrating users ' . ($from + 1) . ' to ' . ($to < $count ? $to : $count));

            $userPreferences = UserPreferences::skip($from)->limit($chunkSize)->get();

            foreach ($userPreferences as $preferences) {
                $userWorkoutPreference = WorkoutPreference::where('user_id', $preferences->user_id)->first();
                if ($userWorkoutPreference) {
                    $this->updateWorkoutPreference($userWorkoutPreference, $preferences);
                } else {
                    $this->createWorkoutPreference($preferences);
                }
            }
        }
    }

    /**
     * @param $userWorkoutPreference
     * @param $userPreferences
     */
    private function updateWorkoutPreference(
        WorkoutPreference $userWorkoutPreference,
        UserPreferences $userPreferences
    ): void {
        $userWorkoutPreference->update([
           'exercise_level_id' => $userPreferences->exercise_level_id,
           'exercise_location_id' => $userPreferences->exercise_location_id,
           'exercise_frequency_id' => $userPreferences->exercise_frequency_id
        ]);
    }

    /**
     * @param $user
     */
    private function createWorkoutPreference(UserPreferences $userPreferences)
    {
        WorkoutPreference::create([
            'user_id' => $userPreferences->user_id,
            'schedule' => null,
            'exercise_level_id' => $userPreferences->exercise_level_id,
            'exercise_location_id' => $userPreferences->exercise_location_id,
            'exercise_frequency_id' => $userPreferences->exercise_frequency_id
        ]);
    }
}
