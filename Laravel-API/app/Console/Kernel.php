<?php

namespace Rhf\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Rhf\Console\Commands\Competitions\CloseCompetition;
use Rhf\Console\Commands\Renewal\ReminderEmail;
use Rhf\Console\Commands\Subscriptions\AddMissingSubs;
use Rhf\Console\Commands\Subscriptions\ShopifySubscriptions;
use Rhf\Console\Commands\Subscriptions\UpdateDirectDebitInfo;
use Rhf\Console\Commands\Subscriptions\UpdateUsersGoCardless;
use Rhf\Console\Commands\Videos\EnableScheduledVideos;
use Rhf\Console\Commands\Feature\EnableFeature;
use Rhf\Console\Commands\Workouts\PreferencesMigration;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ShopifySubscriptions::class,
        ReminderEmail::class,
        UpdateDirectDebitInfo::class,
        UpdateUsersGoCardless::class,
        AddMissingSubs::class,
        CloseCompetition::class,
        EnableFeature::class,
        PreferencesMigration::class,
        EnableScheduledVideos::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('renewal:reminder')
            ->dailyAt('6:00');
        $schedule->command('competition:close')
            ->dailyAt('23:59');
        $schedule->command('videos:enable-scheduled-videos')
            ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
