# Linux-service resource roadmap

**Assessment date: 2026-11-02. Twenty-four documented service locations: eighteen README reservations, five external-candidate references and one location with three disposable-VM systemd units. Production templates: none.**

This is the per-service register for [resources/ROADMAP.md](../ROADMAP.md). It does not select engines, grant privileges or mark Core capabilities Demonstrated. Status values:

| Status | Meaning |
| :--- | :--- |
| Reservation | Reviewed README reservation only; no indexed candidate template or local implementation. |
| Candidate-external | Executable candidate text lives under `agents/wcpd/candidate/planner/templates`. |
| Candidate-local | Files exist here but are disposable-VM / fixture-identity only. |
| Planned-gap | Additional capability recorded in an owner/index; no service implementation or selected provider. Requested README reservations are documentation, not executable/configuration delivery. |

## Delivery order

Implement in this sequence unless an accepted contract says otherwise. The candidate still defaults to Nginx/FPM. The [adopted target for new general-purpose installations](../../src/Module/Hosting/WEB-SERVER-STRATEGY.md) is Nginx -> Apache event -> FPM after explicit versioned implementation and qualification; existing profiles are not reinterpreted. MySQL, MariaDB and Redis remain explicitly selected providers. A README does not change native installation.

1. **R1 web candidate:** nginx, apache, php-fpm, frankenphp and caddy pointers (one canonical Go template source; the target three-layer renderer remains missing).
2. **R2 native control plane:** systemd (production-shaped), postgresql (panel), nginx (panel origins), logrotate and selected firewall/time-sync/confinement baselines. Confinement and time prerequisites belong to M2/M3 native admission, not a later operational phase.
3. **R3 tenant hosting:** M4 quota enforcement before admitting hosting, then nginx/Apache/FPM target profile, qualified alternatives, DNS and ACME. New production topology follows the Hosting web-server roadmap.
4. **R4 hosted data:** postgresql hosted profiles, then mysql **or** mariadb as selected.
5. **R5 mail:** postfix, dovecot, rspamd together.
6. **R6 platform:** SSH, remaining firewall, selected Redis/backup/journal resources and subsequent time/confinement/quota improvements. This phase does not defer the M2/M3 time/confinement or M4 quota prerequisites above.
7. **R7 extended indexes:** applications, host, observability, toolbox, extensions and optional AI/MCP under their existing owners; Docker workloads follow Hosting WS6 and applicable data/recovery contracts.

Shared apply path remains: authorized request → domain policy → durable plan → render → syntax/policy check → approved primitive → observe health → record or reconcile. See [configuration change lifecycle](README.md#configuration-change-lifecycle).

## Per-service register

| Service | Owner | Status | Core milestone | First deliverable | Open decisions / missing work |
| :--- | :--- | :--- | :--- | :--- | :--- |
| [nginx](nginx/README.md) | Hosting | Candidate-external | [M5](../../ROADMAP.md#m5), panel slice [M3](../../ROADMAP.md#m3) | Existing Go Classic/Advanced/application templates; future versioned panel/tenant includes and target Apache backend | Managed include per package, log isolation, graceful reload, TLS ownership; no overwrite of operator vhosts; new default driver missing |
| [apache](apache/README.md) | Hosting | Candidate-external | [M5](../../ROADMAP.md#m5) | Existing FPM/FrankenPHP application previews; future private event backend for target default and dedicated FPM alternative | New Nginx/Apache/FPM driver absent; modules/directives/flags policy, all-request authorization, private listeners and TLS ownership |
| [php-fpm](php-fpm/README.md) | Hosting | Candidate-external | [M5](../../ROADMAP.md#m5) | Per-version pool template: Unix identity, socket mode, children, timeouts, directive allowlist | Panel vs tenant pools; PHP 8.x matrix vs Core PHP 8.4; extension policy |
| [frankenphp](frankenphp/README.md) | Hosting | Candidate-external | [M5](../../ROADMAP.md#m5) | Keep generic Worker, Modern and classic application sources in Go planner | Classic vs opted-in Worker, ZTS extensions, per-customer process isolation, private/direct topology, native install, drain/health/recovery |
| [dns](dns/README.md) | Hosting | Reservation | [M5](../../ROADMAP.md#m5) | Zone/record templates for **one** selected backend | BIND vs PowerDNS (or other) **unset**; DNSSEC key storage; primary/secondary; reverse DNS |
| [acme](acme/README.md) | Hosting | Reservation | [M5](../../ROADMAP.md#m5) | First reviewed client profile only after client choice | lego vs certbot vs other; HTTP-01/DNS-01; wildcard; deploy to mail |
| [postfix](postfix/README.md) | Mail | Reservation | [M8](../../ROADMAP.md#m8) | Main/master fragments, virtual maps, TLS, submission | Topology (local vs relay); open-relay tests; credential maps outside Git |
| [dovecot](dovecot/README.md) | Mail | Reservation | [M8](../../ROADMAP.md#m8) | Auth adapter, mailbox location, IMAP/TLS, sieve | Auth against Core identities; quota; POP optional |
| [rspamd](rspamd/README.md) | Mail | Reservation | [M8](../../ROADMAP.md#m8) | Local controller lockdown, milter/proxy, rate limits | Antivirus (ClamAV) as selected extra vs plugin; DKIM signing home (Rspamd vs OpenDKIM) |
| [postgresql](postgresql/README.md) | Data | Reservation | Control plane [M3](../../ROADMAP.md#m3); hosted [M6](../../ROADMAP.md#m6) | Split profiles: panel cluster vs hosted tenant cluster | HBA/unix sockets; PostgreSQL 17 pin vs future majors; never reuse Symfony `DATABASE_URL` here |
| [mysql](mysql/README.md) | Data | Reservation | [M6](../../ROADMAP.md#m6) | Hosted-engine profile if MySQL is the selected extra engine | Package source; port/socket conflict with MariaDB |
| [mariadb](mariadb/README.md) | Data | Reservation | [M6](../../ROADMAP.md#m6) | Hosted-engine profile if MariaDB is selected | Independent qualification from MySQL; same-host coexistence refused by default |
| [redis](redis/README.md) | Operations | Reservation | [M2](../../ROADMAP.md#m2)/[M9](../../ROADMAP.md#m9) | Optional loopback/unix-socket instance with ACL | Not required for candidate jobs (PostgreSQL claims exist); never expose publicly; separate cache vs queue |
| [systemd](systemd/README.md) | Platform | Candidate-local | [M2](../../ROADMAP.md#m2)/[M3](../../ROADMAP.md#m3) | Replace fixture units with canonical `wcpd`/`wcp-privd`/worker units | Current files: `candidate/wcp-candidate-*.service` with `wcp-*-test` identities and `WCP_ISOLATED_TEST_VM=1` |
| [ssh](ssh/README.md) | Platform | Reservation | [M9](../../ROADMAP.md#m9) | `sshd_config.d` drop-in, Match blocks for SFTP, host-key handling | Admin lockout prevention; tenant keys owned by IdentityAccess/Tenancy |
| [firewall](firewall/README.md) | Platform | Reservation | [M3](../../ROADMAP.md#m3)/[M9](../../ROADMAP.md#m9) | Policy inputs + one backend renderer | nftables vs ufw vs firewalld **unset**; IPv6; management-plane recovery |
| [logrotate](logrotate/README.md) | Observability | Reservation | [M3](../../ROADMAP.md#m3)/[M9](../../ROADMAP.md#m9) | `/etc/logrotate.d/wcp360` for panel logs | Customer vhost/mail logs; copytruncate vs reopen; redaction |
| [backup](backup/README.md) | BackupRestore | Reservation | [M7](../../ROADMAP.md#m7) | Local tool profile: schedule inputs, encryption key **reference**, restore-verify | restic/borg/other **unset**; must work without vendor plugin |
| [quotas](quotas/README.md) | Tenancy / Platform | Reservation | [M4](../../ROADMAP.md#m4) | Profile mapped to `filesystem.quota.apply` once contracts are wired | Backend unset; not a Files-module template dump |
| [time-sync](time-sync/README.md) | Setup / Platform | Reservation | [M3](../../ROADMAP.md#m3) | chrony or timesyncd profile for install preflight | Per-target default |
| [confinement](confinement/README.md) | Platform | Reservation | [M2](../../ROADMAP.md#m2) | Selected MAC profiles for agent/broker/pools/workers | Debian/Ubuntu provider/enforcing-mode qualification; compose with Unix/systemd controls; no added OS claim |
| [caddy](caddy/README.md) | Hosting | Candidate-external | [M5](../../ROADMAP.md#m5) | Existing application FPM and separate embedded-Modern/classic Go pointers; no copied Caddyfiles | Standalone provider, modules, listener/TLS/admin ownership and native lifecycle still unqualified |
| [docker](docker/README.md) | Platform / Hosting / Operations | Reservation | [M5](../../ROADMAP.md#m5), [M9](../../ROADMAP.md#m9), Hosting WS6 | Admitted engine/workload profiles and bounded build/rebuild/lifecycle specification | Rootless/cgroups, image/plugin provenance, private ingress/networks, UID/volumes, observed quotas and recovery; no generic Docker executor |
| [journald](journald/README.md) | Observability / Platform | Reservation | [M9](../../ROADMAP.md#m9) | Selected journal/drop-in profile with read identities and retention/rate/storage budgets | Distinct from logrotate and mandatory business audit; redaction, bounded export, loss/exhaustion and restart/reboot behavior |

The six non-service families are indexed under [resources](../README.md): applications, host, observability, toolbox, extension contributions and optional AI providers/models/assistants/MCP. They are excluded from the 24-service count and create no implementation or new permissions.

## Candidate artifacts that must not be treated as production

| Path | Bound to | Production blocker |
| :--- | :--- | :--- |
| `agents/wcpd/candidate/planner/templates/nginx.conf.tmpl` | `.test` plans, inert render | Real domains, TLS, managed include, apply/health |
| `.../php-fpm.conf.tmpl` | Fixed pool model | Per-site versions/extensions, tenant UID, live reload |
| `.../Caddyfile.tmpl`, `nginx-frankenphp.conf.tmpl`, `frankenphp.service.tmpl` | Opt-in Worker plan/2 | Native install, drain/switch, health and selected confinement |
| `.../Caddyfile-modern.tmpl`, `frankenphp-modern.service.tmpl` | Separate direct Worker plan/3 preview | No durable selection, allocated public listener, TLS ownership or native activation |
| `.../applications/*.tmpl` | Six PHP application recipes, three routing profiles | Includes Apache/Caddy/FPM/FrankenPHP classic; no new default hybrid driver, live app installation or general production qualification |
| `systemd/candidate/wcp-candidate-agent.service` | `User=wcp-agent-test`, no capabilities | Canonical agent identity and packaging paths |
| `systemd/candidate/wcp-candidate-broker.service` | Disposable VM broker | Minimal-root production `wcp-privd` contract |
| `systemd/candidate/wcp-candidate-vm-arm.service` | `WCP_ISOLATED_TEST_VM=1` boot re-arm | Must never ship on a production host |

Debian 13 package path examples in the [service index](README.md#debian-reference-examples) were checked for nginx-common and php8.4-fpm only. Every other destination remains a conventional example until the installed package is resolved.

## Tests required when a template first lands

Record results as `passed` / `failed` / `not_run` / `not_configured` / `not_applicable`. A missing scanner is not a pass.

| Check | Purpose |
| :--- | :--- |
| Renderer/escaping unit tests | Injection, unexpected newlines, path characters |
| Synthetic fixtures under `tests/Fixtures` only | No production-like secrets; no duplicated validators |
| `nginx -t` / `php-fpm -t` / equivalent on disposable target | Syntax is necessary, not sufficient |
| Failed-apply and uncertain-outcome integration | Previous observed state or explicit repair |
| Ownership, symlink and path-allowlist refusals | Traversal and confused-deputy |
| Anonymous / wrong role / wrong tenant / CSRF where HTTP triggers apply | Authorization at the use case, not in the template |
| Cross-service TLS deploy (when claimed) | Nginx and mail see the same live certificate set |

Windows development hosts skip POSIX service syntax; those rows stay `not_run` locally and must execute on Linux CI or a disposable VM.

## Keeping this register honest

- Move a row off Reservation only when actual reviewed configuration/profile/unit source exists in that directory, or an accurate pointer identifies its canonical external candidate source. Adding or editing a README alone does not change implementation status.
- Adding `templates/` without an adapter, agent primitive and tests does not advance Core maturity.
- Backend selection (DNS, firewall, ACME, backup, MySQL vs MariaDB) is an Engineering/Security contract change, not a README edit.
- Update this file, [resources/ROADMAP.md](../ROADMAP.md), the service README and [CAPABILITY-MAP.md](../../CAPABILITY-MAP.md) together when behavior changes.

Related: [Core structure](../../STRUCTURE.md), [service index](README.md), [resources README](../README.md), [Core roadmap](../../ROADMAP.md), [Hosting](../../src/Module/Hosting/README.md), [Mail](../../src/Module/Mail/README.md), [Data](../../src/Module/Data/README.md), [Platform](../../src/Module/Platform/README.md).
