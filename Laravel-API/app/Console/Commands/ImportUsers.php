<?php

namespace Rhf\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rhf\Modules\User\Models\User;
use Illuminate\Support\Facades\Storage;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from the csv file at storage/users.csv.';

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
        //Open the file.
        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $file = $storagePath . 'users.csv';
        $handle = fopen($file, 'r');

        //Loop through the CSV rows.
        $imported = 0;
        $updated = 0;
        $failed = 0;
        $total = 0;
        $i = 0;
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            echo '.';

            // Process the headers if it's the first row
            if ($i > 0) {
                if (strpos($row[4], '/') !== false) {
                    $date = explode('/', $row[4]);
                    $expires = $date[2] . '-' . $date[1] . '-' . $date[0];
                } else {
                    $expires = $row[4];
                }

                // Create valid expires
                if ($expires == '0000-00-00') {
                    $expires = '2000-01-01';
                }

                // Set the expiry time
                $expires .= ' 23:59:59';

                $data = [
                    'first_name' => $row[0],
                    'surname' => $row[1],
                    'email' => $row[2],
                    'password' => bcrypt(Str::random(10)),
                    'paid' => $row[3],
                    'expiry_date' => \Carbon\Carbon::parse($expires),
                ];

                // Check for existing
                if (!User::where('email', '=', $data['email'])->count()) {
                    try {
                        User::create($data);
                        $imported++;
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                        $failed++;
                    }
                } else {
                    try {
                        $user = User::where('email', '=', $data['email'])->first();
                        $user->expiry_date = $data['expiry_date'];
                        $user->save();
                        $updated++;
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                        $failed++;
                    }
                }
                $total++;
            }

            $i++;
        }

        echo $total . ' user imports attempted, ' . $imported . ' created, '
            . $updated . ' updated, ' . $failed . " failed.\r\n";
    }
}
