<?php

namespace Rhf\Console\Commands\Subscriptions;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class AddMissingSubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:add-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update to fill in missing users';

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
        $this->processCSV();
    }

    public function processCSV()
    {
        $filename = storage_path('app/subscriptions/Ashbourne_latest.csv');

        $rows = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $count = 0;
        $chunkSize = 200;
        $totalCount = count($rows);
        $totalChunks = ceil($totalCount / $chunkSize);

        while ($count < $totalChunks) {
            $start = $count * $chunkSize;
            $end = $start + $chunkSize;
            if ($end > $totalCount) {
                $end = $totalCount;
            }
            $this->processChunk($rows, $start, $end);
            $count++;
        }
    }

    public function processChunk($rows, $start, $end)
    {
        $emailsToCheck = [];
        for ($i = $start; $i < $end; $i++) {
            $emailsToCheck[] = $rows[$i][20];
        }

        $existingUsersWithoutSubs = User::with('subscriptions')
            ->whereIn('email', $emailsToCheck)
            ->get()
            ->filter(fn ($i) => $i->subscriptions->count() == 0)
            ->reduce(function ($carry, $item) {
                $carry[$item->email] = $item;
                return $carry;
            }, []);

        $data = [];
        foreach ($emailsToCheck as $i => $email) {
            if (isset($existingUsersWithoutSubs[$email])) {
                $data[] = [
                    'user' => $existingUsersWithoutSubs[$email],
                    'dd_reference' => $rows[$i][3],
                    'created_at' => $rows[$i][15]
                ];
            }
        }
        $this->createUserSubscriptions($data);
    }

    public function createUserSubscriptions($data)
    {
        $subs = collect($data)->map(function ($item) {
            $user = $item['user'];
            $date = Carbon::createFromFormat('d/m/Y H:i:s', $item['created_at']);
            $created = $date->format('Y-m-d H:i:s');

            return [
                'user_id' => $user->id,
                'email' => $user->email,
                'subscription_provider' => 'directdebit',
                'subscription_plan' => 'standard',
                'subscription_frequency' => 'monthly',
                'purchase_date' => !is_null($item['created_at']) ? $created : $user->created_at,
                'subscription_reference' => !is_null($item['dd_reference']) ? $item['dd_reference'] : null,
                'expiry_date' => $user->expiry_date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        UserSubscriptions::insert($subs);
    }
}
