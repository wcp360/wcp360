# Observability

The [Toolbox design](design/toolbox/README.md) prepares named bounded diagnostics and keeps repairs with their resource owners. [Diagnostic resource profiles](../../../resources/toolbox/README.md), [observability resources](../../../resources/observability/README.md) and the [Core structure](../../../STRUCTURE.md) distinguish product diagnostics from developer tools. These planning locations enable no new tool or endpoint.

**Status: executable candidate administrator audit metadata query and browser/API presentation. Production audit policy, retention, logs, metrics, incidents and alert delivery remain unfinished.**

## Candidate audit inspection

| Source | Responsibility |
| :--- | :--- |
| [AuditListQuery](Domain/AuditListQuery.php) | Exact event/actor/tenant/resource/request filters, inclusive UTC date bounds and shared cursor transport limits. |
| [ListAuditEvents](Application/ListAuditEvents.php) | Read-only fixed audit metadata projection with current active-ADMIN admission, query scope and final authority check. |
| [AuditController](UI/Http/AuditController.php) | Completed ADMIN session required for browser and same-origin cookie API reads. |
| [Audit view](../../../templates/observability/audit.html.twig) | WCP360 admin layout, escaped event text, filter form, empty state and shared cursor navigation. |
| [Role upgrade](../Setup/Infrastructure/CandidateDatabaseRoles.php) | Explicit setup-only, fixed ADMIN SELECT upgrade with transactional audit, no USER/worker read grant or audit mutation grant. |

Routes are `GET /workbench/audit` and `GET /workbench/v1/audit`. The administration sidebar and command palette link the journal. Anonymous browser requests retain the login redirect; API requests retain the shared 401 envelope. USER sessions and active tenant membership never grant global audit visibility. A ROOT/ADMIN panel identity is not the Linux root account. Optional MFA stays optional; an enabled factor and current session admission remain enforced by existing guards.

The candidate ADMIN surface intentionally sees audit metadata across candidate tenants and system events. This is not a reseller, delegated tenant or production audit permission. Every pivot/page/boundary query includes the shared current active-administrator predicate, with another check before return. The API exposes only event ID, actor/tenant/resource UUIDs, event kind, creation time and nullable request reference. It does not join account/contact/credential tables or return plan/configuration contents, passwords, MFA/session material, raw request data or arbitrary logs. Existing writer redaction obligations still apply; this reader does not sanitize arbitrary secret-bearing records after ingestion.

## Filters and navigation

All query values must be bounded strings. Only these fixed fields are supported:

| Field | Accepted value |
| :--- | :--- |
| `kind` | Exact lowercase ASCII event kind, 1–64 letters/digits/underscores/dots, starting with a letter; empty means all. |
| `actor`, `tenant`, `resource` | Exact canonicalizable RFC 4122 UUID; empty means all, including null/system records. A filter is not an authority grant. |
| `request` | Exact existing 32-character lowercase hexadecimal diagnostic reference; empty means all. |
| `from`, `until` | Valid full UTC `YYYY-MM-DDTHH:MM:SSZ`, inclusive; a reversed range is refused. |
| `limit` | 25 (default), 50 or 100. |
| `after`, `before` | One exclusive event UUID cursor; both directions together are refused. |

Unknown keys, arrays, nonstrings, SQL/sort/column selectors, wildcard event kinds, invalid dates and invalid identifiers fail with 400. Missing or filter-excluded pivots also fail with 400. Empty results and null/system references remain valid. Ordering is creation UTC text descending, then event UUID descending; backward navigation reverses the bounded read. Filters reset cursors. No total-count query, offset, arbitrary search SQL or unbounded export exists.

This is live navigation, not a snapshot: newer insertions can appear at the beginning. Limits bound response rows; they do not qualify maximum database size/query work. Large-volume query/index/retention qualification remains open. Reads do not append business audit, avoiding recursive audit-read events, and do not change recorded state. Existing HTTP no-store/CSP/no-referrer headers apply; HEAD has no response body and mutating methods are unsupported.

## Restricted PostgreSQL opt-in

Fresh candidate roles remain audit INSERT-only. No grant is silently added by an HTTP request, migration, ordinary worker or default role creation. Shared maintenance development connections can read existing metadata; session-isolated ADMIN connections require the reviewed setup action below. Missing SELECT returns a generic 503, without fallback to maintenance credentials.

From an already trusted checkout and private setup connection, with existing candidate migrations and verified roles, inspect:

```sh
WCP_CONTEXT=setup php bin/console wcp:candidate:database:roles --audit-reads
```

Explicit application additionally requires the existing `WCP_ALLOW_CANDIDATE_DATABASE_ROLES=1` gate:

```sh
WCP_CONTEXT=setup WCP_ALLOW_CANDIDATE_DATABASE_ROLES=1 php bin/console wcp:candidate:database:roles --audit-reads --apply
```

The new mode and `--password-changes` are mutually exclusive. It verifies the existing role identities and fixed grants before applying only `SELECT ON public.wcp_candidate_audit` to the fixed ADMIN role. It uses the existing advisory lock and transaction/statement/lock bounds, preserves role IDs/passwords and audits a real transition once. Audit failure rolls the grant back; uncertain commit results remain uncertain. Unexpected USER/worker read grants or other privilege drift are refused, not repaired. Audit UPDATE/DELETE/TRUNCATE and grant options remain refused. Existing password-operation inspection/upgrade accepts either explicit ADMIN audit-read state.

No new schema, migration, dependency, service or local process restart is introduced. No opt-in or migration was applied locally. This is development candidate capability admission, not production authorization or native host provisioning.

## Evidence and remaining scope

[Query definitions](../../../tests/Unit/AuditListQueryTest.php), [repository tests](../../../tests/Integration/ListAuditEventsTest.php), [HTTP journeys](../../../tests/Integration/AuditJourneyTest.php), [real PostgreSQL role/upgrade checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) and [CLI refusal checks](../../../tests/Integration/CandidateDatabaseRolesCommandTest.php) cover transport/calendar/identity bounds, timestamp ties and two-way traversal, exact correlation filters, inserts, missing/excluded cursors, nullable projection, current/racing admin revocation, SQLite, anonymous/USER refusal, optional-MFA login, escaping, methods/HEAD/no-store, explicit grants, rollback, idempotence and drift refusal. Existing hosting/site suites cover shared parser compatibility.

Executed results must be bound to their exact source and workflow in draft PR #116. Local executable tests remain stopped by user instruction. Passing this slice does not complete M1/M2/M9, establish production readiness or implement raw log access, retention, audit integrity signatures, diagnostic bundles, metrics or alerts.

- [Core roadmap](../../../ROADMAP.md)
- [Core navigation](../../../NAVIGATION.md)
- [Controlled service resources](../../../resources/services/README.md)
