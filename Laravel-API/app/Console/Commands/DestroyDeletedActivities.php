<?php

namespace Rhf\Console\Commands;

use Illuminate\Console\Command;
use Rhf\Modules\Activity\Models\ActivityDeletion;

class DestroyDeletedActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:destroy-deletions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy all deleted activities (irreversible)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = ActivityDeletion::count();
        if ($count == 0) {
            echo "No Activities to delete!\n";
            return;
        }

        echo "Deleting $count Activities\n";
        echo "THIS ACTION IS IRREVERSIBLE!!!\n";

        if (!$this->confirm('Do you wish to continue?')) {
            echo "No Activities were destroyed!\n";
            return;
        }

        $deleteCount = ActivityDeletion::query()->delete();
        echo "Deleted $deleteCount Activities!\n";
    }
}
