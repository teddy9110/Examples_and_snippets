<?php

namespace Rhf\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Activity\Models\ActivityDeletion;

class DeleteDuplicateActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:purge-duplicates {max=500}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge duplicate activities';

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
        $maxEntries = $this->argument('max');
        echo "Deleting a maximum of $maxEntries Activity duplicates groups...\n";

        $subQuery = Activity::query()
            ->select(['user_id', 'type', 'date'])
            ->whereIn('type', ['fat', 'carbohydrates', 'fiber', 'protein', 'calories'])
            ->groupBy(['user_id', 'type', 'date'])
            ->having(DB::raw('count(*)'), '>', 1)
            ->limit($maxEntries);

        $query = Activity::query()
            ->joinSub($subQuery, 'activity2', function ($join) {
                $join
                    ->on('activity.user_id', '=', 'activity2.user_id')
                    ->on('activity.type', '=', 'activity2.type')
                    ->on('activity.date', '=', 'activity2.date');
            });

        DB::transaction(function () use ($query) {
            $groupCount = 0;
            $deleteCount = 0;

            $query->chunk(1000, function ($activities) use (&$groupCount, &$deleteCount) {
                $idsToDelete = [];
                $data = [];

                $activityGroups = $activities
                    ->groupBy(function ($item) {
                        return $item->user_id . ':' . $item->type . ':' . $item->date;
                    })
                    ->map(function ($item) {
                        return $item[0];
                    });

                foreach ($activityGroups as $activityGroup) {
                    $groupItems = $activities
                        ->where('user_id', $activityGroup->user_id)
                        ->where('type', $activityGroup->type)
                        ->where('date', $activityGroup->date)
                        ->sortByDesc('updated_at');

                    $groupItems->shift();

                    foreach ($groupItems as $activity) {
                        $data[] = $activity->toArray();
                        $idsToDelete[] = $activity->id;
                    }

                    $groupCount++;
                }

                ActivityDeletion::insert($data);
                $deleteCount += Activity::whereIn('id', $idsToDelete)->delete();
            });

            echo "Deleted $deleteCount Activities within $groupCount groups!\n";
        });
    }
}
