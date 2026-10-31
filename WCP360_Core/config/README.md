# Symfony application configuration

This directory owns framework/library configuration, service-container wiring, routes and application context bindings. **It does not hold applied Nginx, Apache, PHP-FPM, mail or database-server configuration.** Find those source resources in [resources/services](../resources/services/README.md).

| Location | Responsibility |
| :--- | :--- |
| [packages](packages/README.md) | Symfony and installed library settings; environment-specific settings when implemented |
| [routes](routes/README.md) | Reviewed HTTP routing and controller imports |
| [contexts](contexts/README.md) | Document ADMIN, USER and worker identities, sessions, connections and allowed imports |
| Future services.yaml or services.php | Explicit service discovery/wiring, including domain-namespaced Console classes |
| Future bundles.php | Reviewed first-party framework bundles; detached plugins do not register arbitrary Core bundles |

These are documented reservations. The actual approved Symfony bootstrap must supply and validate its configuration files; README directories are not executable Symfony configuration. contexts/ is a WCP360 documentation boundary and is not automatically loaded by Symfony.

Use typed validated settings and per-identity secret references. Keep global platform policy in the Platform domain and resource-specific policy in its owning module. Do not use one production .env or universal credential set across ADMIN, USER, workers and agents.

The application boundary follows [Symfony configuration conventions](https://symfony.com/doc/current/configuration.html). See [Core navigation](../NAVIGATION.md), [dependency ownership](../DEPENDENCIES.md) and the [current scope](../../documentation/manifests/WCP360-BM-0001.md).
