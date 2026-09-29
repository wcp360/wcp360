# Uninstall / Decommission Policy

## Principle

Uninstalling WCP360 is not equivalent to deleting hosted customer data.

## Modes

### Remove control plane only
Remove:
- WCP binaries;
- web UI;
- daemon;
- internal runtime.

Preserve:
- tenant data;
- service configs unless explicitly converted/exported;
- backups;
- audit export.

### Full decommission
Potentially removes:
- WCP-managed generated configs;
- WCP system users/groups;
- internal state.

Tenant data deletion requires explicit separate confirmation.

## Before uninstall

Provide:
- inventory;
- hosted resources;
- dependent services;
- configuration export;
- backup status;
- admin warning.

## Safety

Never recursively delete `/srv/wcp360` as an automatic consequence of package removal.

Data deletion must be explicit and separately authorized.