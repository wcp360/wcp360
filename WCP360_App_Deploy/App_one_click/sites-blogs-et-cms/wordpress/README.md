# WordPress candidate recipe

**Status: authoring pilot only. No application version/artifact is selected, no install/update/restore adapter is implemented, and no installation is available.**

[recipe.json](recipe.json) binds the exact catalogue tuple WordPress / Sites, blogs et CMS / Application / PHP. The source CSV/Markdown/XLSX are unchanged. Its local dev recipe ID is not a Core resource or permission. [configuration.schema.json](configuration.schema.json) describes draft inputs; it does not create an instance or resolve their references.

## Requirements and boundaries

As checked on 2026-11-02, [WordPress recommends](https://wordpress.org/about/requirements/) PHP 8.3+, MariaDB 10.11+ or MySQL 8.0+, and HTTPS. These are upstream recommendations, not a version-pinned WCP360 support claim. Its datastore is not the Core PostgreSQL database. A future release must select and qualify PHP/extensions, engine, application version, artifact digest and distribution/licensing inputs.

The target general-purpose topology is Nginx -> Apache event -> PHP-FPM; the current Core candidate still defaults to Nginx/FPM. Existing [PHP profiles](../../../../WCP360_Core/src/Module/Hosting/APPLICATION-PROFILES.md) generate inert text, not WordPress installations. Nginx managed routing and native Apache .htaccess are distinct capabilities; WordPress does not universally require .htaccess. Plugin-dependent native rules need their own qualification. [WordPress Nginx documentation](https://developer.wordpress.org/advanced-administration/server/web-server/nginx/).

FrankenPHP classic is a separate candidate preference, supported by an [upstream guide](https://frankenphp.dev/docs/wordpress/); Worker remains disabled and unqualified. No arbitrary plugin/theme compatibility is implied.

Inputs use registered tenant/account/domain/database/PHP/capacity references, bounded admin/site metadata and secret references only. The pilot authoring locales are en_US/fr_FR; they are not an installed-language guarantee. Salts, admin/database credentials and actual resource IDs are not committed.

## Lifecycle plans

| Phase | Plan |
| :--- | :--- |
| install | [install](lifecycle/install.md) |
| configure | [configure](lifecycle/configure.md) |
| update | [update](lifecycle/update.md) |
| uninstall | [uninstall](lifecycle/uninstall.md) |
| backup | [backup](lifecycle/backup.md) |
| restore | [restore](lifecycle/restore.md) |
| inspect | [inspect](lifecycle/inspect.md) |
| reconcile | [reconcile](lifecycle/reconcile.md) |

Application code, database, uploads, configuration and required plugin/theme inventory need consistent recovery. Uninstall retains data; purge is a separate authorization. See the [schema guide](../../../schemas/README.md), [Core application owner](../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md) and [hosting roadmap](../../../../WCP360_Core/src/Module/Hosting/WEB-SERVER-ROADMAP.md).
