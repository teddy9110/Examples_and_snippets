<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnualEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $name;
    private $date;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $date
     */
    public function __construct($name, $date)
    {
        $this->name = $name;
        $this->date = $date;

        $this->subject = 'Welcome to Team RH - How to get started!';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('orders.welcome-annual-email', [
            'name' => $this->name,
            'date' => $this->date
        ]);
    }
}
