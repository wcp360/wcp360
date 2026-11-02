# PHP application recipe preparation

**Status: candidate source layout. No one-click installer or qualified PHP application is supplied by this directory.**

This family prepares recipes for catalogue PHP applications. Preserve the original category as display metadata; use reviewed category/application path slugs for source navigation. A folder name is neither a canonical Core resource ID nor a permission.

The initial [WordPress draft](sites-blogs-et-cms/wordpress/README.md) belongs to the catalogue category Sites, blogs et CMS. Use the shared [authoring template](../_templates/application/README.md), [candidate schema](../schemas/app-recipe.schema.json) and [lifecycle](../docs/LIFECYCLE.md). Do not duplicate all 137 PHP entries as empty folders.

## Required recipe boundaries

Pin application/recipe versions, source artifacts, PHP/runtime/extensions, database and other dependencies, supported topology/provider/OS, data/configuration paths owned by the Core, secrets by reference, resource budgets and recovery. Configuration is typed; any installer/build code requires its own admitted non-root execution boundary.

The target general-purpose topology is Nginx → Apache event → FPM, with all site requests traversing Apache. It remains a future implementation: existing Core Classic Nginx/FPM and the six preview recipes retain their meanings. Native .htaccess, multi-PHP and FrankenPHP classic/Worker compatibility must be qualified for the exact application/version, not inferred from a PHP label.

Installation, configuration, updates, backup/restore and retained-data uninstall enter the same owning Core use cases. A recipe cannot create its own root command, access the panel database or modify Core dependencies. Purge is separate and explicitly authorized.

A PHP library, WordPress/database extension, project template or unresolved identity must use its own workflow. The [catalogue mapping](../catalogue/README.md) describes these distinctions; it enables no operation.

Follow [qualification](../docs/QUALIFICATION.md) before advertising a one-click action. Folder presence, source links and a syntactically valid draft are insufficient.
