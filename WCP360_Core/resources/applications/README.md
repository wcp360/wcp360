# Application resource register

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owner: [Hosting](../../src/Module/Hosting/README.md), with Platform, Operations, Data, BackupRestore and Tenancy for their existing responsibilities.

| Family | Canonical source and qualification boundary |
| :--- | :--- |
| Traditional PHP, front controllers, WordPress reference | [Application profiles](../../src/Module/Hosting/APPLICATION-PROFILES.md); Go remains the only candidate template source. |
| Native HTTP services | Registered provider/process, private ingress, health, graceful drain, workers and dependencies. |
| Docker/OCI multi-service workloads | [Docker provider](../services/docker/README.md), admitted build/image inputs, networks, volumes and observed quotas. |
| Discourse reference | [Hosting strategy](../../src/Module/Hosting/WEB-SERVER-STRATEGY.md) and [roadmap](../../src/Module/Hosting/WEB-SERVER-ROADMAP.md); upstream Docker lifecycle, internal Nginx, application-owned data and migrations. |

Future resource text belongs to a versioned recipe and existing owner. Do not duplicate installers/catalogues or turn every proposed plugin into a Core folder. Support is specific to version, features, topology, provider and tested OS/architecture; a proxy response is not lifecycle qualification.

Native .htaccess and Worker requirements remain explicit. Application data, dependencies and secrets stay outside source. Optional recipes use [extension boundaries](../extensions/README.md); Core retains inventory and recovery when a plugin is disabled.

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
