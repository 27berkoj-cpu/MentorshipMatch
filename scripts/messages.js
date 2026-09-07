// Messaging browser code.
//
// The server must verify that the current user belongs to a conversation
// before returning or saving messages. The browser is responsible for reading
// the conversation, sending new text, and showing success or error feedback.
//
// Recommended API calls:
// - GET /api/messages/:userId to load a conversation.
// - POST /api/messages to send a message.
// - Include the recipient id and message text in the request body.
// - Do not trust an id from the page as proof of authorization.

