# Error-page templates

**Status: template source location reserved; no custom error handler is implemented here.**

Place approved browser-facing not-found, denied, unavailable and unexpected-error presentations here. Keep page layout context and localization explicit. Reusable inline alerts and operation feedback belong in components/feedback rather than duplicated error pages.

Present safe messages and an allowed correlation reference without leaking stack traces, secret values, private paths or another tenant's resource existence. Exception-to-status/error-code mapping remains in the approved server boundary; a template must not invent production error contracts or authorization behavior.

- [Template map](../README.md)
- [Feedback components](../components/feedback/README.md)
- [Localization](../../translations/README.md)
- [Browser verification](../../tests/Browser/README.md)
