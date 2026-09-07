// Mentor search and profile browser code.
//
// Load mentor data from the backend rather than keeping a hard-coded list in
// the page. Search terms and filters should be sent to the API, which can use
// parameterized SQL to return relevant results safely.
//
// Recommended flow:
// 1. Read the search input and selected filters.
// 2. Call GET /api/mentors with URL query parameters.
// 3. Check response.ok and handle a 401 response.
// 4. Render returned mentor data into the page.
// 5. Link each result to student/mentor-profile.html?id=<mentor id>.

