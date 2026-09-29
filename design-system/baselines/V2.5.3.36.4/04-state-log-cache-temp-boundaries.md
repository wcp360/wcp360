# State / Log / Cache / Temp Boundaries

## Persistent state

`/var/lib/wcp360`

Contains:
- job execution state;
- resource registry;
- desired-state revisions;
- reconciliation markers;
- migration metadata;
- installer completion state.

## Logs

`/var/log/wcp360`

Separate:
- web logs;
- daemon logs;
- jobs;
- audit forwarding/output;
- installer.

Logs must be:
- rotated;
- bounded;
- secret-redacted;
- correlation-aware.

## Cache

`/var/cache/wcp360`

May contain:
- package metadata;
- generated previews;
- discovery cache.

Deleting cache must not destroy authoritative state.

## Temporary job data

`/var/tmp/wcp360`

For:
- exports;
- staging artifacts;
- temporary generated archives.

Every temp artifact must have:
- owner job;
- expiry;
- cleanup state.

## Disk pressure

WCP should reserve enough operational headroom to:
- write logs;
- complete critical state transactions;
- clean temporary artifacts.

Disk-full handling must not corrupt desired-state metadata.