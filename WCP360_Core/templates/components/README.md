# Shared first-party Twig components

**Status: reusable presentation source locations prepared; no component implementation or registration is supplied here.**

| Component source | Responsibility |
| :--- | :--- |
| [forms](forms/README.md) | Reusable labeled fields, help text, error summaries, validation feedback and accessible field groups. |
| [tables](tables/README.md) | Reusable table headers, rows, empty/loading states, pagination, filters, sorting and bounded-export controls. |
| [feedback](feedback/README.md) | Notifications, operation status, confirmations, loading/error/empty states and accessible dialogs. |
| [navigation](navigation/README.md) | First-party menus, breadcrumbs, tabs, pagination navigation and command-palette presentation. |

Use one shared presentation component across module pages instead of duplicating form controls, table chrome or dialogs. Keep domain decisions and authorization in server application services. Browser interactions and style tokens have their own source under [assets](../../assets/README.md).

Review the existing [prototype component gallery](../../../WCP360_Prototype/components.html) as design/interaction evidence. Port accepted patterns into the real Twig/UX pipeline; prototype JavaScript state is not a production backend and is not automatically loaded by Core.

The selected bootstrap defines component names, loader paths and registration. Verify native focus, form feedback and context handling through [browser tests](../../tests/Browser/README.md). Detached plugin presentation remains isolated from the Core Twig loader.

- [Template source map](../README.md)
- [Core source map](../../NAVIGATION.md)
