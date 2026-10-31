# Frontend source map

**Status: frontend source locations documented; the Symfony frontend and its build/bootstrap are not implemented by these README files.**

| Source | Responsibility |
| :--- | :--- |
| [controllers](controllers/README.md) | Focused first-party Stimulus interaction controllers |
| [styles](styles/README.md) | Design tokens, component/layout rules, themes and responsive styles |
| [images](images/README.md) | Approved first-party source image assets |

Use the selected Symfony UX/AssetMapper and CSS pipeline when the actual bootstrap is accepted. Twig markup is owned by [templates](../templates/README.md); the owning module supplies authorized data and operations. JavaScript never replaces server-side policy or Linux validation.

Registration, imports, asset names and prepared output are supplied and tested with real implementation. Add Node tooling only for a selected build/test need; node_modules, compiled caches and generated asset output do not become repository source. Production exposes only the approved built assets through [public](../public/README.md).

- [Dependency ownership](../DEPENDENCIES.md)
- [Browser/accessibility verification](../tests/Browser/README.md)
- [Source navigation](../NAVIGATION.md)
