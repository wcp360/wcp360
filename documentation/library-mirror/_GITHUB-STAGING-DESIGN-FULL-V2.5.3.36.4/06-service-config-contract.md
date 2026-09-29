# Service Configuration Contract

## Objective

Define schema-first managed configuration without exposing raw system mutation as the default interface.

## Common flow

`Load current → Validate desired → Compute diff → Save desired state → Apply/Reload → Verify → Result → Audit`

## Required schema metadata

- config domain;
- schema version;
- owning module;
- source/provenance;
- editable fields;
- field types;
- ranges/enums;
- secret fields;
- restart/reload requirement;
- dependency checks;
- validation command/mechanism;
- rollback mechanism;
- generated file targets;
- ownership/mode requirements.

## Managed service families

### Nginx
- listener;
- vhost;
- upstream;
- TLS reference;
- redirects;
- headers;
- rate limits;
- upload/body limits.

### PHP-FPM
- version;
- pool;
- worker policy;
- memory/time/upload limits;
- slowlog;
- extension policy references.

### Redis
- bind;
- port;
- maxmemory;
- eviction policy;
- persistence;
- auth integration.

### Mail
- TLS policy;
- relay policy;
- queue limits;
- antispam integration;
- reputation-related controls.

### MariaDB/PostgreSQL
- connection limits;
- memory/buffer settings;
- logging;
- replication settings;
- restart-required classification.

## Raw editing

Raw config editing is:
- not default;
- expert-only;
- privileged where system-wide;
- diffed;
- validated;
- provenance-aware;
- auditable.

WCP-managed generated files must clearly state they may be overwritten by desired-state reconciliation.