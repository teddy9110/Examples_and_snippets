<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $name;
    private $date;
    private $ukSignupUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $date, $ukSignupUrl)
    {
        $this->name = $name;
        $this->date = $date;
        $this->ukSignupUrl = $ukSignupUrl;

        $this->subject = 'Welcome to Team RH - How to get started!';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('orders.welcome-email', [
            'name' => $this->name,
            'date' => $this->date,
            'ukSignupUrl' => $this->ukSignupUrl
        ]);
    }
}
