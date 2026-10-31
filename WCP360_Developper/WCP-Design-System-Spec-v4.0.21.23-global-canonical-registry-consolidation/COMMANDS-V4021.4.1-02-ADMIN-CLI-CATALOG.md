# V4.0.21.4.1 — WCP-ADMIN CLI Catalog

## Familles à prévoir
`server`: status/inspect/reconcile/drain; `service`: list/status/start/stop/restart/reload/reconcile; `account`: create/update/suspend/resume/terminate/reconcile; `reseller`: create/update/suspend/resume/ceiling-set; `plan`: create/clone/update/archive/assign/impact; `entitlement`: effective/override-set/reset; `quota`: show/usage/set/reconcile; `isolation`: profile-list/profile-apply/effective/events/verify; `domain`; `site`; `webserver`; `php`; `tls`; `dns`; `db`; `cache`; `mail`; `mail-security`; `access`; `git`; `app`; `wordpress`; `runtime`; `cron`; `worker`; `backup`; `security`; `firewall`; `monitor`; `logs`; `job`; `demo`; `trial`; `migration`; `update`; `install`; `doctor`; `audit`.

Les opérations destructives, rotation de clés, changements firewall à risque, réduction de ceiling et security-floor exigent confirmation, reason/audit et step-up selon politique.
