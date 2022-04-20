<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompetitionEnded extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $competition;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($competition)
    {
        $this->competition = $competition;
        $this->subject = 'Competition: ' . $competition->title . ' has ended';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.competition-ended', [
            'competition' => $this->competition
        ]);
    }
}
