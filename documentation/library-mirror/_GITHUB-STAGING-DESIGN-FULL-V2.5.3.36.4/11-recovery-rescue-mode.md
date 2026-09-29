# Recovery / Rescue Mode

## Purpose

Provide a bounded operational recovery path when the normal web layer or configuration is broken.

## Recovery scenarios

- WCP web UI unavailable;
- daemon not starting;
- invalid generated Nginx config;
- firewall lockout;
- corrupted WCP metadata;
- failed upgrade;
- lost admin MFA access.

## Principles

Recovery tooling must not become a permanent generic root shell feature in WCP.

## Rescue capabilities

Specification may include:
- validate WCP config;
- restore previous WCP config revision;
- restore previous firewall policy;
- reset WCP admin authentication through out-of-band server access;
- rebuild runtime socket dirs;
- reconcile job/state database;
- disable a broken generated service fragment;
- inspect health summary.

## Authentication

Rescue assumes OS-level root/console access.

It is outside normal WCP-USER flows.

## Audit

If audit subsystem is available, rescue operations should be recorded.
Otherwise a durable recovery log must be imported after service restoration where feasible.