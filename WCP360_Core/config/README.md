# Symfony application configuration

This directory owns framework/library configuration, service-container wiring, routes and application context bindings. **It does not hold applied Nginx, Apache, PHP-FPM, mail or database-server configuration.** Find those source resources in [resources/services](../resources/services/README.md).

| Location | Responsibility |
| :--- | :--- |
| [packages](packages/README.md) | Symfony and installed library settings; environment-specific settings when implemented |
| [routes](routes/README.md) | Reviewed HTTP routing and controller imports |
| [contexts](contexts/README.md) | Document ADMIN, USER and worker identities, sessions, connections and allowed imports |
| [services.yaml](services.yaml) | Implemented service discovery/wiring, context bindings and candidate database middleware |
| [bundles.php](bundles.php) | Installed framework bundle registration; detached plugins do not register arbitrary Core bundles |

The executable candidate supplies Symfony bootstrap, service and package configuration. README-only reservations remain unfinished. `contexts/` documents WCP360 runtime boundaries and is not automatically loaded by Symfony. The implemented [candidate database profiles](contexts/DATABASE-PROFILES.md) are wired explicitly through Doctrine configuration and services.

Use typed validated settings and per-identity secret references. Keep global platform policy in the Platform domain and resource-specific policy in its owning module. Do not use one production .env or universal credential set across ADMIN, USER, workers and agents.

The application boundary follows [Symfony configuration conventions](https://symfony.com/doc/current/configuration.html). See [Core navigation](../NAVIGATION.md), [dependency ownership](../DEPENDENCIES.md) and the [current scope](../../documentation/manifests/WCP360-BM-0001.md).
