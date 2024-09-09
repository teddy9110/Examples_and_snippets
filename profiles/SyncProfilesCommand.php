<?php

namespace App\Console\Commands;

use App\Models\UserProfile;
use App\Events\Publishes\ProfileUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

/**
 * @codeCoverageIgnore
 */
class SyncProfilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topics:sync {days=1 : The number of days to go back}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sync user profile information into SFMC";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = $this->argument('days');
        $updatedSince = Carbon::now()->subDays($days);

        UserProfile::whereRaw("(
                user_id in (select distinct(user_id) from user_profile_learning_providers as uplp where (uplp.`current` = 1 or uplp.future = 1) and uplp.updated_at > '$updatedSince')
                or user_id in (select distinct(user_id) from user_profile_subjects as ups where (ups.`current` = 1 or ups.future = 1) and ups.updated_at > '$updatedSince')
                or user_id in (select distinct(user_id) from user_profile_qualifications as upq where upq.updated_at > '$updatedSince')
                or user_id in (select distinct(user_id) from user_marketing_preferences as ump where ump.updated_at > '$updatedSince')
                or user_id in (select distinct(user_id) from user_profile_topics_of_interest as upti where upti.updated_at > '$updatedSince')
            )")
            ->orWhere('updated_at', '>', $updatedSince)
            ->orderBy('user_id')
            ->chunk(1000, function ($users) {
                foreach ($users as $user) {
                    // Emit a Profile Updated event for each user to force an update into api.marketing
                    ProfileUpdated::publish($user);
                }
            });
    }
}
