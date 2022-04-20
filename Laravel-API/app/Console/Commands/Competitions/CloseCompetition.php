<?php

namespace Rhf\Console\Commands\Competitions;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Rhf\Mail\CompetitionEnded;
use Rhf\Modules\Competition\Models\Competition;

class CloseCompetition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes competition once end date has passed';

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
        $competition = Competition::where('closed', 0)
            ->whereActive(1)
            ->where('end_date', '<', now())
            ->get();

        foreach ($competition as $comp) {
            $comp->update([
                'closed' => true
            ]);

            $this->competitionEmail($comp);
        }
    }

    private function competitionEmail($competition)
    {
        $competitionAdmins = config('app.competition_admins');

        $emailUsers = $competitionAdmins;
        foreach ($emailUsers as $email) {
            Mail::to($email)
                ->send(new CompetitionEnded($competition));
        }
    }
}
