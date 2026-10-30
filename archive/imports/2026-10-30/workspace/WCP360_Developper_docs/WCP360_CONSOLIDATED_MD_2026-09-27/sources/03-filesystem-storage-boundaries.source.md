# Filesystem & Storage Boundaries

## Objective

The web layer and daemon must never operate on arbitrary filesystem locations supplied directly by a user.

## Proposed managed roots

Example logical roots only; exact paths remain part of the system specification review.

### System-owned WCP root
Purpose:
- binaries;
- immutable application assets;
- internal schemas.

Must not contain tenant content.

### WCP configuration root
Purpose:
- WCP application configuration;
- generated service desired-state metadata.

### Runtime root
Purpose:
- Unix socket;
- pid/lock material;
- short-lived runtime metadata.

### State root
Purpose:
- job metadata;
- internal state;
- revisions;
- reconciliation data.

### Log root
Purpose:
- WCP logs;
- daemon logs;
- audit forwarding buffers where applicable.

### Managed hosting data root
Purpose:
- tenant/application files;
- databases only via engine-specific storage;
- generated site structures.

## Boundary rules

1. Tenant file operations resolve from application/resource ID.
2. Symlink traversal outside the managed root must be prevented.
3. Canonical path resolution must happen before privileged action.
4. User-supplied path fragments cannot become absolute paths.
5. `..` traversal rejected after normalization.
6. Mount-point crossings must be intentional and policy-aware.
7. Device files, procfs, sysfs, socket files and sensitive system paths are never exposed through File Manager.
8. WCP-generated config files are not ordinary tenant-editable content.
9. Secrets must not be stored in normal editable site files by default.
10. Backups must not be written into the same failure domain by default when an off-site repository is configured.

## File-manager boundary

WCP-USER may access only the registered application root.

No UI-level “go to /”.

No symbolic link may provide escape to:
- `/etc`;
- `/root`;
- other accounts;
- WCP internal state;
- daemon socket;
- database engine storage;
- secret stores.

## Generated config provenance

Every WCP-managed file should have metadata:
- owning module;
- resource ID;
- schema version;
- generated revision;
- last apply job;
- checksum.

Manual modifications outside WCP may be detected as drift.

## Deletion

Recursive deletion must:
- be resource-bound;
- reject filesystem root/mount roots;
- support dry impact summary;
- use soft delete/trash where appropriate for tenant files.
