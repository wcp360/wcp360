# ROOT service observations

**Status: a read-only Platform development candidate. Service control, health verification and production provider qualification remain open.**

## Ownership and entry points

[ServiceOverview](Application/ServiceOverview.php) uses the narrow [ServiceObservation](Application/ServiceObservation.php) port. [SystemdServiceObservation](Infrastructure/SystemdServiceObservation.php) collects local systemd properties; the [catalogue](Domain/ServiceUnitCatalogue.php) owns the fixed lookup set. Hosting, Data and Operations retain their service-specific configuration and health policies.

ROOT opens `GET /workbench/services` from the sidebar, command palette or [server overview](../../../templates/platform/server.html.twig). `GET /workbench/v1/services` returns `wcp360.candidate.service-overview/1`; HEAD has no response body. Both surfaces require a fully authenticated current ADMIN account. Current authority is checked before collection and again before returning the result. A hosting account named `root` gains no administrator authority. Optional MFA remains optional; an enabled factor must be completed.

Every query parameter is refused. Mutating methods are unsupported. The shared HTTP boundary retains private/no-store responses and safe errors. No request can choose a unit, command, property, file path, transport or remote host.

## Fixed observations

| Family | Business owner | Candidate unit lookups |
| :--- | :--- | :--- |
| Nginx | Hosting | `nginx.service` |
| Apache | Hosting | `apache2.service` |
| PHP-FPM | Hosting | `php8.1-fpm.service` through `php8.5-fpm.service` |
| Caddy | Hosting | `caddy.service` |
| FrankenPHP | Hosting | `frankenphp.service` |
| PostgreSQL | Data | `postgresql.service` |
| MySQL / MariaDB | Data | `mysql.service`, `mariadb.service` |
| Redis | Operations | `redis-server.service` |
| Docker | Platform | `docker.service` |

These nine families and fourteen exact names are lookup candidates, not accepted provider identifiers or an installation matrix. The FPM names reflect the existing five-target package catalogue; they do not expand supported PHP versions. Aliases may resolve to the same canonical unit: identical records collapse, conflicting duplicates fail collection. MySQL and MariaDB aliases do not prove two installed engines.

A per-application or embedded FrankenPHP process may have no matching fixed unit. PostgreSQL's umbrella unit may be active/exited while a particular database is unavailable. A not-found unit does not prove that software is absent. This inventory observes the **current process namespace**, which may be a container rather than the whole server.

## Report semantics

| Field | Meaning |
| :--- | :--- |
| `status=observed` | A nonempty, bounded response passed strict parsing. Individual lookups can still be missing or not found. |
| `status=not_configured` | The supported local observation prerequisites are unavailable, including Windows or a namespace without the expected system manager. |
| `status=failed` | Collection, its deadline/output limits or parsing failed. Raw diagnostics are withheld. |
| `observation=observed` | At least one validated unit record matches the family. Read the load/active/sub states; this is not a health result. |
| `observation=not_observed` | A successful collection returned no matching record for that family. Installation and runtime state remain unknown. |
| `observation=unavailable` | Collection was unavailable or failed; the family has no inferred state. |

The report always retains `coverage_complete=false`, `control_available=false` and `health_verified=false`. It exposes the collection timestamp, static labels/owners and only unit ID, load state, active state, substate and optional unit-file state. Unknown bounded state words remain opaque. It returns no PID, command line, environment, status text, journal, credentials or raw output. The existing server-overview version-1 response remains independent and unchanged.

## Bounded native adapter

The adapter is inert at construction. On a recognized Debian 12/13 or Ubuntu 22.04/24.04/26.04 Linux target with the required local manager, a non-root process can make one fixed `systemctl show` call. The executable and parent chain must satisfy the adapter's ownership/path policy. The argument vector selects exactly the five properties above and the fourteen fixed names. There is no shell, sudo, caller-supplied selector, service mutation or remote/user-bus control.

The single child has a three-second budget, at most 32,768 stdout bytes and 8,192 stderr bytes. Its explicit minimal environment excludes inherited DBus/systemd transport settings, loader variables and application secrets. Standard input is closed; errors and output overflow produce a generic failed result. Strict parsing refuses empty output, malformed or duplicate properties, unknown canonical unit IDs, controls, conflicting records and excessive values/blocks.

systemd documents `show` for machine-readable properties and `--all` for retaining empty properties. The parser uses named properties rather than positional line order. See the [official systemctl manual](https://www.freedesktop.org/software/systemd/man/latest/systemctl.html).

## Verification and remaining work

[Parser cases](../../../tests/Unit/ServiceObservationSnapshotTest.php), [authority and reporting cases](../../../tests/Integration/ServiceOverviewTest.php), [adapter contracts](../../../tests/Integration/SystemdServiceObservationTest.php) and [ROOT HTTP journeys](../../../tests/Integration/ServiceJourneyTest.php) define this slice. Synthetic observations establish contract behavior; they do not establish native service availability. Local executable checks remain stopped. Executed results must identify the exact source in [draft PR #116](https://github.com/wcp360/wcp360/pull/116).

A container without a local systemd manager should report not configured. Passing a refusal/fixture case there does not qualify successful native observation. Qualification still needs a disposable supported systemd host, a non-root panel identity, alias/missing-unit cases, measured deadline behavior and a check that observation never mutates service state.

Future management requires separately reviewed typed operations, current authority and effect fencing, durable plans/audit, provider-specific configuration validation, post-change health and recovery. This read path creates no system-control permission, canonical agent command, database grant, schema change or provider admission. The [24-family resource register](../../../resources/services/ROADMAP.md), [M9](../../../ROADMAP.md#m9) and other production milestones remain open.
