@component('mail::message')
# ❌ RSVP Cancelled

Hello {{ $userName }},

Your RSVP for **{{ $eventTitle }}** has been **cancelled**.

---

**Event Details:**
- 📅 Date: {{ \Carbon\Carbon::parse($eventDate)->format('l, F j, Y') }}
- 📍 Location: {{ $eventLocation }}
- 🪑 Seat: {{ $seatLabel }}
- 🏷️ Status: Cancelled

---

If this was a mistake or you’d like to rebook, please contact our support team.

Regards,  
**{{ config('app.name') }} Team**
@endcomponent
