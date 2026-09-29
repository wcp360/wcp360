# Secret Boundary Contract

## Secret classes

- account password hashes;
- generated credentials;
- API token secrets;
- webhook signing secrets;
- private keys;
- database passwords;
- application secret variables;
- provider credentials;
- recovery codes.

## Visibility classes

### `SHOW_ONCE`
Allowed immediately after generation:
- API token;
- generated DB password;
- webhook secret;
- recovery codes.

### `NEVER_REVEAL`
After creation:
- stored passwords;
- token secret;
- private key;
- webhook secret.

A replacement/rotation creates a new secret; it does not reveal the old one.

## Storage rules

- encrypted secret subsystem;
- no plaintext application database columns;
- no normal logs;
- no audit payloads;
- no job result payloads;
- no support bundle unless specifically permitted and safely transformed—which should default to exclusion.

## Daemon interface

Prefer secret references/handles instead of returning secret material to the web layer.

If material must cross the socket for a bounded deployment action:
- minimum lifetime;
- no echo in response;
- zero/cleanup where practical;
- redaction in errors.