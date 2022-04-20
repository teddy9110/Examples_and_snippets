<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerServicesRenewalList extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $users;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;

        $this->subject = 'List of Renewals sent: ' . now()->format('d/m/Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.CustomerServiceList', [
            'users' => $this->users
        ]);
    }
}
