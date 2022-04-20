<?php

namespace Rhf\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserStoryEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->data['first_name'] . ' ' . $this->data['second_name'];
        return $this->subject('User Story: ' . $name)
            ->view('emails.UserStory', [
            'data' => $this->data
        ])
        ->attach($this->data['before_photo'], ['as' => 'Before Photo.jpeg', 'mime' => 'image/jpeg'])
        ->attach($this->data['after_photo'], ['as' => 'After Photo.jpeg', 'mime' => 'image/jpeg']);
    }
}
