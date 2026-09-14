// Session booking browser code.
//
// Students should see only available mentor time slots. The server must check
// availability again when a booking is submitted because another student may
// have booked the slot after the page loaded.
//
// Recommended flow:
// 1. Load availability with GET /api/availability/:mentorId.
// 2. Let the student choose a slot.
// 3. Send POST /api/sessions with the mentor and slot ids.
// 4. Show the confirmed session only after the server responds successfully.
// 5. Use DELETE /api/sessions/:id to cancel, with server-side authorization.

