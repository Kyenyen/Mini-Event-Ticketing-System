@component('mail::message')
# 🎉 RSVP Confirmed!

Hello {{ $userName }},

Your RSVP for **{{ $eventTitle }}** has been successfully confirmed!

---

**Event Details:**
- 📅 Date: {{ \Carbon\Carbon::parse($eventDate)->format('l, F j, Y') }}
- 📍 Location: {{ $eventLocation }}
- 🪑 Seat: {{ $seatLabel }}
- 🏷️ Status: Confirmed

---

Thank you for joining us — we can’t wait to see you there!

Regards,  
**EventTicket Team**
@endcomponent
