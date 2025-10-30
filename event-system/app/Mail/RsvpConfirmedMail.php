<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Rsvp;

class RsvpConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rsvp;

    public function __construct(Rsvp $rsvp)
    {
        $this->rsvp = $rsvp;
    }

    public function build()
    {
        $event = $this->rsvp->event;
        $userName = $this->rsvp->guest_name ?? $this->rsvp->user->name;
        $seatLabel = $this->rsvp->seat->label ?? 'Assigned by system';

        return $this->subject('RSVP Confirmed: ' . $event->title)
                    ->markdown('emails.rsvp.confirmed')
                    ->with([
                        'eventTitle' => $event->title,
                        'eventDate' => $event->date,
                        'eventLocation' => $event->location,
                        'userName' => $userName,
                        'seatLabel' => $seatLabel,
                    ]);
    }
}
