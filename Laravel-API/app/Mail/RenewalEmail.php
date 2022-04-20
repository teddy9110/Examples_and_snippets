<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenewalEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $name;
    private $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;

        $this->subject = 'Reminder Email: Your subscription is coming to an end.';
    }

    /**
     * Build the message.
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.renewal', [
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
