# Server Filesystem Layout

## Principles

The filesystem layout must separate:
- immutable binaries;
- application code;
- configuration;
- secrets;
- runtime sockets/PIDs;
- persistent state;
- logs;
- caches;
- temporary files;
- generated service configuration;
- tenant/application data.

## Proposed canonical layout

```text
/usr/local/wcp360/
├── bin/
├── lib/
└── share/

/etc/wcp360/
├── wcp360.conf
├── policies/
├── schemas/
└── service-registry/

/var/lib/wcp360/
├── state/
├── jobs/
├── revisions/
├── resources/
├── reconciliation/
└── metadata/

/var/log/wcp360/
├── web/
├── daemon/
├── jobs/
├── audit/
└── installer/

/run/wcp360/
├── wcpd.sock
├── locks/
└── runtime/

/var/cache/wcp360/
├── packages/
├── metadata/
└── generated/

/var/tmp/wcp360/
└── transient-jobs/

/etc/wcp360/secrets/
└── encrypted secret material / references

/srv/wcp360/
├── accounts/
├── apps/
├── backups-cache/
└── staging/
```

## Path classification

### `/usr/local/wcp360`
Immutable product/runtime assets.

Writable only during controlled installation/upgrade.

### `/etc/wcp360`
System-wide WCP configuration.

Must not contain tenant content.

### `/var/lib/wcp360`
Persistent internal state and desired-state metadata.

Not web-readable directly.

### `/run/wcp360`
Ephemeral runtime state.

Recreated after reboot.

Contains daemon socket and locks.

### `/var/log/wcp360`
Product logs and audit-related service logs.

Write access split by component.

### `/var/cache/wcp360`
Rebuildable cache only.

Never authoritative state.

### `/var/tmp/wcp360`
Transient large job data with cleanup policy.

### `/srv/wcp360`
Managed hosting data root.

Tenant/application content is subordinate to registered resource roots.

## Forbidden placement

Do not store WCP persistent state in:
- `/tmp`;
- web document roots;
- tenant homes;
- `/root`;
- `/usr/share/nginx/html`;
- arbitrary `public_html` directories.

## Mount boundaries

If `/srv/wcp360` is a dedicated filesystem, mount characteristics should be documented.

WCP must detect unexpected read-only state, inode exhaustion and mount loss.