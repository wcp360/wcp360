# Application and provider deployment preparation

**Status: candidate source layout. No container manager, native installer or production deployment is implemented here.**

DEPLOY prepares versioned application/provider recipes under the original catalogue categories. The initial [Discourse draft](forums-modernes/discourse/README.md) belongs to Forums modernes. The folder name does not make every DEPLOY, ISOLE, SERVICE or INFRA entry an interchangeable Docker application.

Use the [shared template](../_templates/application/README.md), [candidate schema](../schemas/app-recipe.schema.json), [Core boundary](../docs/CORE-INTEGRATION.md) and [lifecycle](../docs/LIFECYCLE.md). Do not generate placeholder directories for every source row.

## Separate delivery families

| Family | Required boundary |
| :--- | :--- |
| Native/container application | Hosting instance and private ingress, admitted Platform provider, data/dependencies, secrets, health, limits and durable lifecycle. |
| Service/runtime/infrastructure | Existing Core resource owner and target-specific provider policy; no tenant replacement of panel web/database/mail services. |
| Project template/static build/build tool | Isolated project build and publication; no production exposure of a development server or Core dependency changes. |
| Application/database extension | Explicit compatible parent resource and scoped activation; no independent application identity by default. |
| AI CPU/GPU or tool-using application | Qualified model/backend/hardware, secrets/egress, isolation and budgets; no implicit Core tool authority. |
| Cluster/lab/VM | Separate nodes/isolation, administration, capacity, networking, licensing and failure/recovery qualification. |

Discourse remains its own upstream application workflow with version-pinned build/rebuild inputs, private HTTP ingress, database/cache dependencies, jobs and data-aware upgrades/restoration. A public proxy response is insufficient evidence of lifecycle support.

A recipe contributes typed requirements; it cannot submit arbitrary Compose, shell hooks, host mounts, root options or Docker API calls. Builds execute code and require an admitted isolated builder. Provider profiles must prove actual limits, ownership and private networking on each claimed target.

Uninstall retains data by default; purge, dependency removal and essential adapter replacement require distinct authorized decisions. A recipe/plugin failure or commercial expiry must not erase inventory, stop unrelated hosting or remove essential Core recovery.

See [qualification](../docs/QUALIFICATION.md) and the [catalogue audit](../docs/CATALOGUE-AUDIT.md). Every source entry remains a candidate, not an enabled deployment.
