# API Reference — Mini Event Ticketing System

This document lists the main API endpoints used by the SPA. It's a compact reference — the project also includes a Postman collection at `docs/postman_collection.json` for import.

Authentication
--------------
- The application uses Laravel Sanctum for SPA authentication (cookie-based).
- Flow (from frontend):
  1. GET /sanctum/csrf-cookie (establish CSRF cookie)
  2. POST /api/login with JSON body { email, password } (sent with credentials)
  3. Subsequent requests to protected endpoints should be sent with cookies (axios: withCredentials = true)

Common headers:
- Accept: application/json
- Content-Type: application/json

Auth endpoints
--------------
- POST /api/register
  - Body: { name, email, password, password_confirmation }
  - Responses: 201 user created, 422 validation errors

- POST /api/login
  - Body: { email, password }
  - Responses: 200 user object, 401 invalid credentials

- POST /api/logout
  - Requires auth (cookie)
  - Response: 204

Events
------
- GET /api/events
  - List events (public)

- GET /api/events/{id}
  - Get event details

- POST /api/events (admin)
  - Create event (admin-only)
  - Body: { title, description, date, location, capacity }
  - Responses: 201 created, 422 validation

- PUT /api/events/{id} (admin)
  - Update event

Seats
-----
- GET /api/events/{event}/seats
  - Returns array of seats for the event with status: available, processing, booked, blocked

- POST /api/seats/{seat}/block (admin)
  - Toggle block/unblock for a seat

RSVPs
-----
- POST /api/events/{event}/rsvp (authenticated user)
  - Body: { seat_id }
  - Behavior: server enforces "one confirmed RSVP per user per event"; will reject attempts to change a confirmed RSVP.
  - Responses: 201 created (rsvp), 400/409 business rule, 422 validation

- POST /api/events/{event}/guest-rsvp (guest)
  - Body: { guest_name, guest_email }
  - Server will reuse canceled guest RSVPs when possible to avoid unique constraint errors.

- POST /api/rsvps/{rsvp}/cancel
  - Cancel an RSVP (user or admin)

Responses and error handling
---------------------------
- Validation failures: HTTP 422 with { errors: { field: [messages] } }
- Business rule violations: HTTP 400 or 409 with message explaining the restriction (e.g., "User already has a confirmed RSVP for this event").

Tips for testing with Postman
----------------------------
- Import `docs/postman_collection.json` into Postman.
- Ensure you first call `GET /sanctum/csrf-cookie` (this sets cookies). In Postman, enable "Automatically follow redirects" and ensure cookies are retained between requests.
- When calling protected endpoints, Postman must send cookies. The easiest approach is to run a small browser-based test or use the SPA configured to communicate with the backend.

If you want, I can expand this file into a full OpenAPI (Swagger) spec or generate a more detailed collection with example responses for each endpoint.
