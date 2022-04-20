<?php

namespace Rhf\Console\Commands\Renewal;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Rhf\Mail\CustomerServicesRenewalList;
use Rhf\Mail\RenewalEmail;
use Rhf\Modules\User\Models\User;

class ReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewal:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs command to send reminder email to users';

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
        $end = now()->addMonth()->endOfDay();

        $users = User::select('users.email', 'users.first_name', 'users.surname', 'users.expiry_date')
            ->join('user_subscriptions', 'user_subscriptions.user_id', 'users.id')
            ->where('user_subscriptions.subscription_frequency', 'annual')
            ->where('user_subscriptions.subscription_provider', 'shopify')
            ->ActivePaidCustomer()
            ->where('users.expiry_date', $end)
            ->get();

        if (count($users) > 0) {
            $this->sendList($users);
            $this->sendEmail($users);
        }
    }


    public function sendEmail($users)
    {
        $count = 0;
        while ($count < count($users)) {
            $name = $users[$count]->first_name . ' ' . $users[$count]->surname;
            $email = $users[$count]->email;
            Mail::to($email)->send(new RenewalEmail($name, $email));
            $count++;
        }
        $this->run = false;
    }

    public function sendList($users)
    {
        $emailUsers = [
            'david.murray@teamrhfitness.com',
            'kaspars.zarinovs@teamrhfitness.com',
        ];
        foreach ($emailUsers as $email) {
            Mail::to($email)
                ->send(new CustomerServicesRenewalList($users));
        }
    }
}
