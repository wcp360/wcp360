# Form presentation components

**Status: first-party Twig source location reserved; no executable component is supplied here.**

Reusable labeled fields, help text, error summaries, validation feedback and accessible field groups.

Business rules remain in Domain/Application services; Symfony form/HTTP mapping stays in the owning module. A Twig component displays accepted validation results and does not become another validation engine.

Share presentation through this directory. DOM behavior belongs in approved assets/controllers and style rules in assets/styles; avoid parallel component copies in each business domain. Component naming and loader wiring must be verified when the actual Twig/UX bootstrap exists.

- [Shared component map](../README.md)
- [Frontend controller sources](../../../assets/controllers/README.md)
- [Browser/accessibility verification](../../../tests/Browser/README.md)
- [Core source index](../../../NAVIGATION.md)
