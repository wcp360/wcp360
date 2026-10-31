# Data-table presentation components

**Status: first-party Twig source location reserved; no executable component is supplied here.**

Reusable table headers, rows, empty/loading states, pagination, filters, sorting and bounded-export controls.

Render server-provided authorized data and query state. Selection, export, column preferences and bulk actions must preserve the concrete resource scope and server-enforced bounds; visual filtering is not authorization.

Share presentation through this directory. DOM behavior belongs in approved assets/controllers and style rules in assets/styles; avoid parallel component copies in each business domain. Component naming and loader wiring must be verified when the actual Twig/UX bootstrap exists.

- [Shared component map](../README.md)
- [Frontend controller sources](../../../assets/controllers/README.md)
- [Browser/accessibility verification](../../../tests/Browser/README.md)
- [Core source index](../../../NAVIGATION.md)
