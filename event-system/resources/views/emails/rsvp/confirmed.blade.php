@component('mail::message')
# 🎟 RSVP Confirmed

Hi {{ $rsvp->guest_name ?? $rsvp->user->name }},

Your RSVP for **{{ $rsvp->event->title }}** has been confirmed!

**Event Details:**
- 📅 Date: {{ $rsvp->event->date->format('F j, Y') }}
- 📍 Location: {{ $rsvp->event->location }}
- 🪑 Status: {{ ucfirst($rsvp->status) }}

@component('mail::button', ['url' => url('/')])
View Event
@endcomponent

Thanks for your RSVP!  
See you at the event 🎉  
{{ config('app.name') }}
@endcomponent
