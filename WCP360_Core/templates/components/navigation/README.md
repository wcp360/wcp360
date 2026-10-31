# Navigation components

**Status: first-party Twig source location reserved; no executable component is supplied here.**

First-party menus, breadcrumbs, tabs, pagination navigation and command-palette presentation.

Consume the approved scoped route/contribution registry. Hiding a link does not enforce authorization; optional plugin contributions cannot inject arbitrary Core Twig or JavaScript.

Share presentation through this directory. DOM behavior belongs in approved assets/controllers and style rules in assets/styles; avoid parallel component copies in each business domain. Component naming and loader wiring must be verified when the actual Twig/UX bootstrap exists.

- [Shared component map](../README.md)
- [Frontend controller sources](../../../assets/controllers/README.md)
- [Browser/accessibility verification](../../../tests/Browser/README.md)
- [Core source index](../../../NAVIGATION.md)
