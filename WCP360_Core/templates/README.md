# First-party Twig source map

**Status: template locations documented; no executable Twig pages or component registration are supplied by this scaffold.**

| Source | Responsibility |
| :--- | :--- |
| [admin](admin/README.md) | Administrative pages in their authorized ADMIN context |
| [user](user/README.md) | Customer pages with enforced tenant/resource scope |
| [setup](setup/README.md) | Restricted initial setup and enrollment presentation |
| [layouts](layouts/README.md) | Reusable context-aware page frames |
| [components](components/README.md) | Shared form, table, chart, feedback and navigation presentation |
| [errors](errors/README.md) | Safe context-aware error-page presentations |

Templates consume the owning module's authorized view data. They do not own business rules, privileges, secret storage or system writes. Sharing a layout/component does not share origins, cookies, sessions or runtime credentials.

Browser behavior and styles belong in [assets](../assets/README.md). Linux-service configuration text belongs in [resources/services](../resources/services/README.md), even if the selected renderer eventually uses templates. Keep service rendering separate from the browser Twig loader.

Detached plugin Twig files are never added to the Core loader. Loader paths, names, escaping and component registration must be verified with the actual accepted frontend bootstrap.

- [Runtime contexts](../config/contexts/README.md)
- [Browser/accessibility verification](../tests/Browser/README.md)
- [Core navigation](../NAVIGATION.md)
