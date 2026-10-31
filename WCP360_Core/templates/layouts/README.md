# First-party page layouts

**Status: template source location reserved; no rendered layout is implemented here.**

Place reusable ADMIN and USER page frames here when the Twig bootstrap exists. Reuse the approved components for navigation, form controls and feedback; keep actual business pages in their existing templates/admin and templates/user locations. Setup keeps its restricted flow under templates/setup.

Sharing a base template does not merge origins, cookies, sessions, runtime credentials or permissions. Layout selection is presentation; the implemented context/router/security layers enforce the boundary. Detached plugin templates are not added to the Core loader.

- [Template map](../README.md)
- [Shared components](../components/README.md)
- [Context isolation](../../config/contexts/README.md)
- [Browser verification](../../tests/Browser/README.md)
