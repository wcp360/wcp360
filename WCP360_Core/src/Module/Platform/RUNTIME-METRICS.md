# Read-only server runtime observations

Date: 2026-11-04. Candidate scope: D-CORE-OBS-0001, recorded in PR #135.
This independent feature does not implement or bypass the blocked MFA change.

## Additive candidate report contract

The existing ADMIN-only GET/HEAD overview may include `server.runtime`:

```text
uptime_seconds: integer >= 0 or null
load_average: null or {
  one_minute: finite nonnegative number,
  five_minutes: finite nonnegative number,
  fifteen_minutes: finite nonnegative number
}
```

Existing observer implementations can omit this optional field. The UI then
renders unavailable values, not fabricated zeros. Each observation can fail
independently. No canonical API version, permission, route or request parameter
is introduced. `ServerOverview` and `ServerController` authorization, before/after
principal checks, query rejection, no-store behavior and HEAD semantics remain
unchanged. The local adapter emits the new field; existing fields are preserved.

## Source and limits

`LinuxRuntimeMetrics` reads only `/proc/uptime` and `/proc/loadavg`, at most 4097
bytes each, rejecting records over 4096 bytes. No shell, subprocess, network,
caller path or new daemon action. Reads are independent and on demand; no shared
worker cache. Non-Linux, unreadable, malformed, incomplete or overflowing input
produces null. Parsers require complete ASCII records with the Linux two-decimal
format and validate discarded fields without publishing them.

Uptime is truncated to whole seconds. Aggregate idle time may exceed uptime;
load averages are not CPU percentages. The values describe the visible procfs
namespace, not guaranteed tenant/cgroup allocation or a health/SLO verdict.
The collector publishes no PID, task count, raw input, environment or credential.
Primary semantics: https://www.kernel.org/doc/html/latest/filesystems/proc.html

## Tests and execution

`LinuxRuntimeMetricsTest` provides 24 uptime vectors, 26 load vectors and two
composition/live-shape tests. Four new integration/journey tests cover JSON and
HTML, zero/null distinction, partial availability and read-only behavior. Existing
USER/anonymous/inactive-account/enabled-factor denial and query/method tests remain.
The legacy observer without runtime fields is still exercised by the Windows/UI test.

Local PHP 8.4.24: all six PHP files pass syntax validation; a dependency-free
harness executes the 50 parser vectors plus five composition/live-shape checks,
55/55 successful. Three discarded mutants (uptime off-by-one, omitted size bound,
unknown replaced by zero) are rejected. This is not local PHPUnit/Symfony/Twig or
PostgreSQL execution. Full exact-commit CI is required before acceptance; an old
MFA-tooling success is not evidence for this feature.

No migration, dependency, authentication/MFA runtime modification, new branch,
production deployment or baseline ratification. All new technical identifiers,
comments and tests use English; French labels remain existing UI-language copy.
