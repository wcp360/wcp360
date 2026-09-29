# Secret Storage Placement

## Canonical principle

Secrets are stored outside tenant/application editable roots and outside ordinary web-readable configuration.

## Proposed logical location

```text
/etc/wcp360/secrets/
```

or a dedicated encrypted backend integrated behind a WCP secret abstraction.

## Secret classes

- provider credentials;
- DB generated passwords;
- application secrets;
- API token secrets;
- webhook signing secrets;
- private keys;
- recovery codes;
- internal service credentials.

## Access

- daemon/root-mediated for system secrets;
- web layer receives only bounded secret references where possible;
- decrypted material has minimum lifetime.

## Backups

Secret backup behavior must be explicit:
- encrypted;
- separately protected;
- not included in ordinary support bundles;
- recovery tested.

## Rotation

Every rotatable secret should have:
- creation date;
- last rotation;
- owning resource;
- rotation action;
- dependent service reload/deploy requirements.