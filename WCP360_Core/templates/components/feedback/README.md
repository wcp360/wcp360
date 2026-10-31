# Feedback and dialog components

**Status: first-party Twig source location reserved; no executable component is supplied here.**

Notifications, operation status, confirmations, loading/error/empty states and accessible dialogs.

Use stable operation/error identifiers and translated presentation text. Confirmation is a user interaction, not an authorization grant. Dialog focus and asynchronous outcomes require native browser verification.

Share presentation through this directory. DOM behavior belongs in approved assets/controllers and style rules in assets/styles; avoid parallel component copies in each business domain. Component naming and loader wiring must be verified when the actual Twig/UX bootstrap exists.

- [Shared component map](../README.md)
- [Frontend controller sources](../../../assets/controllers/README.md)
- [Browser/accessibility verification](../../../tests/Browser/README.md)
- [Core source index](../../../NAVIGATION.md)
