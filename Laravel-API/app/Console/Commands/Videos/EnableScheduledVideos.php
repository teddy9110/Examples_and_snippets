<?php

namespace Rhf\Console\Commands\Videos;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Rhf\Modules\Video\Models\Video;

class EnableScheduledVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:enable-scheduled-videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check hourly to see if any videos have passed their time to release,
        setting them to active if true.';

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
        $videos = Video::where('scheduled_date', Carbon::now()->format('Y-m-d'))->where('active', 0)->get();
        foreach ($videos as $video) {
            if (
                is_null($video->scheduled_time) ||
                Carbon::parse($video->scheduled_time)->between(
                    Carbon::now()->startOfHour(),
                    Carbon::now()->endofHour()
                )
            ) {
                $video->active = true;
                $video->order = Carbon::parse($video->scheduled_date)->format('d');
                $video->save();
            }
        }
    }
}
