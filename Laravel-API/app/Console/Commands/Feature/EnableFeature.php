<?php

namespace Rhf\Console\Commands\Feature;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Rhf\Modules\System\Models\Feature;

class EnableFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature:enable-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the date and queries the Features table to
    establish which features need to be enabled if any. Once it has checked if any
    features need to be enabled. Iterated over the returned results and toggles the feature from its previous state
    to its new state. If a feature is enabled a Log::info entry is created to signify what feature was enabled.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = now()->format('Y-m-d');
        $features = Feature::whereActive(0)
            ->whereActiveFrom($date)
            ->get();

        foreach ($features as $feature) {
            $feature->update([
                'active' => !$feature->active,
            ]);
            Log::notice($feature->name . ' made active');
        }
    }
}
