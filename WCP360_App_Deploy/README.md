# WCP360 App Deploy

**Status: candidate catalogue and deployment preparation, 2026-11-02. No application is qualified or made installable by these files.**

WCP360 App Deploy prepares public, versioned application recipes for WCP360 Standalone. It owns catalogue metadata, recipe definitions, compatibility and qualification evidence. The Core retains application instances, authorization, jobs, ingress, providers, secrets, data and recovery. This workspace is independent of the Core Composer/Symfony graph.

## Catalogue and source preservation

The original [CSV](WCP360_App_Catalogue_Candidat.csv), [Markdown](WCP360_App_Catalogue_Candidat.md) and [XLSX](WCP360_App_Catalogue_Candidat.xlsx) are preserved byte for byte. They describe **410 references in 70 categories, 17 natures and 16 profiles**, including applications, services, runtimes, extensions, project templates and libraries.

The CSV is the canonical editorial input for the candidate Core reader. Markdown and XLSX remain the user's preserved companion documents. This decision does not make an editorial row a production recipe or redefine accepted Core contracts. The [audit](docs/CATALOGUE-AUDIT.md) records exact hashes, matching CSV/Markdown content, differing row order and the limits of the XLSX inspection.

Categories and source access/profile labels remain unchanged. [Profile routing](catalogue/README.md) separates their meanings without creating another 410-entry index or automatically assigning privileges.

## Workspace layout

| Location | Responsibility |
| :--- | :--- |
| [catalogue](catalogue/README.md) | Source interpretation and a sixteen-profile metadata mapping. |
| [App_one_click](App_one_click/README.md) | PHP recipe preparation under the original category names, using reviewed path slugs. |
| [DEPLOY](DEPLOY/README.md) | Application/provider deployment preparation; infrastructure, labs and frameworks require their distinct workflows. |
| [Candidate recipe schema](schemas/app-recipe.schema.json) | Draft descriptor validation; not an accepted production manifest or runtime loader. |
| [Application authoring template](_templates/application/README.md) | Preparation guidance, not an installable application. |
| [Core integration](docs/CORE-INTEGRATION.md) | Typed read-only catalogue boundary and future recipe admission. |
| [Lifecycle](docs/LIFECYCLE.md) | Install, configure, update, uninstall, retention and recovery requirements. |
| [Qualification](docs/QUALIFICATION.md) | Ordered delivery gates and pilot acceptance. |

The initial [WordPress](App_one_click/sites-blogs-et-cms/wordpress/README.md) and [Discourse](DEPLOY/forums-modernes/discourse/README.md) locations prepare two different delivery families. They are drafts, not downloaded software or working installers. Do not create 410 placeholder application folders.

## Integration and delivery

The candidate Core catalogue command reads fixed CSV metadata through a typed Hosting port. It performs no installation, package download, provider connection or host change. Every returned candidate remains non-installable. Missing catalogue files must not prevent Core boot; inspect the command's unavailable/failed result instead of inventing success. See the [exact boundary](docs/CORE-INTEGRATION.md).

First preserve and inspect the catalogue, then review the candidate descriptor and pilot recipes. Admit trusted artifacts/providers and implement durable lifecycle operations before adding a live installation action. Qualify install/configure/update/backup/restore/uninstall and their refusal paths on each claimed target. Optional recipes and commercial integrations must not become dependencies of essential hosting or local recovery.

No free-form shell, arbitrary Compose, Core dependency hooks or direct Core-table writes are introduced. Private signing, billing/licensing infrastructure and deployment secrets stay outside this public recipe workspace.
