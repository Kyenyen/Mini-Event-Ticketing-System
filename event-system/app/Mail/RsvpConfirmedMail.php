<?php

namespace App\Mail;

use App\Models\Rsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RsvpConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rsvp;

    /**
     * Create a new message instance.
     */
    public function __construct(Rsvp $rsvp)
    {
        $this->rsvp = $rsvp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your RSVP Confirmation')
                    ->markdown('emails.rsvp.confirmed', [
                        'rsvp' => $this->rsvp,
                    ]);
    }
}
