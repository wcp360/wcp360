# V4.0.21.11 --- Installer Transaction / Resume / Rollback

## Phases

bootstrap identity → directories/ownership → SecretStore → control-plane
DB → daemon/RPC → web panel → service adapters → firewall/exposure →
initial admin → TLS → health → finalization.

## Journal

Chaque phase possède state, inputs hash, outputs refs, start/end, error,
retryability and rollback class. L'installation est resumable après
reboot/panne.

## Idempotence

Rejouer une phase réussie ne duplique pas utilisateurs, secrets, DB ou
règles. Les phases non rollbackables sont explicitement annoncées avant
exécution.

## Sécurité

Temporary bootstrap tokens expirent et sont invalidés à finalization.
Permissions temporaires sont retirées.
