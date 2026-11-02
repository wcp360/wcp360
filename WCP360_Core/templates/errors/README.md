# Error-page templates

**Status: branded candidate exception page implemented; production error contracts remain unfinished.**

[http.html.twig](http.html.twig) extends the existing WCP360 auth layout and official surface branding. It receives only fixed French messages, status and a server-generated request reference from the [shared HTTP responder](../../src/Shared/Http/README.md). The template does not receive the exception, request payload, SQL, private resource details or a stack trace. Content is escaped by Twig.

Authentication redirects and explicitly rendered domain forms stay with their existing owners. Shared heads disable Turbo snapshots; HTTP responses retain no-store, CSP and no-referrer controls. If rendering fails, the responder returns a fixed plain-text fallback without invoking another error template. Neither error presentation nor a reference grants access, retries a mutation or determines an uncertain operation's outcome.

- [Template map](../README.md)
- [Feedback components](../components/feedback/README.md)
- [Shared transport verification](../../tests/Integration/HttpErrorJourneyTest.php)
