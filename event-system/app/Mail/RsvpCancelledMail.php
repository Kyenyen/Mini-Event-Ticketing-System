<?php

namespace App\Mail;

use App\Models\Rsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RsvpCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rsvp;

    public function __construct(Rsvp $rsvp)
    {
        $this->rsvp = $rsvp;
    }

public function build()
    {
        return $this->subject('❌ Your RSVP Has Been Cancelled')
            ->markdown('emails.rsvp.cancelled')
            ->with([
                'userName' => $this->rsvp->guest_name ?? $this->rsvp->user->name ?? 'Guest',
                'eventTitle' => $this->rsvp->event->title ?? 'Event',
                'eventDate' => $this->rsvp->event->date ?? now(),
                'eventLocation' => $this->rsvp->event->location ?? 'TBA',
                'seatLabel' => $this->rsvp->seat->label ?? 'N/A',
                'status' => ucfirst($this->rsvp->status ?? 'Cancelled'),
            ]);
    }
}
