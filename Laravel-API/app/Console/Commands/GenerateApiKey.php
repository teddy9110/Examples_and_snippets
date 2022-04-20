<?php

namespace Rhf\Console\Commands;

use Illuminate\Console\Command;
use Rhf\Modules\Auth\Models\ApiKey;
use Illuminate\Support\Facades\Hash;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a newly hashed API key and return.';

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
        // Generate a string to user as API key
        $key = $this->random(64);

        // Create hashed version
        $hash = Hash::make($key);

        // Insert
        $apiKey = new ApiKey();
        $apiKey->api_key = $hash;
        $apiKey->save();

        echo 'Newly generated API Key: ' . $key . "\r\n";
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    public function random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }
}
