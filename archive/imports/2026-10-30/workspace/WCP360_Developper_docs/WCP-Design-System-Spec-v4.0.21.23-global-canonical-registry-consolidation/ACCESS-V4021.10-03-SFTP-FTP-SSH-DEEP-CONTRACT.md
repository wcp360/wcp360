# V4.0.21.10 --- SFTP / FTP / SSH Deep Contract

## Modes d'accès

`DISABLED`, `SFTP_ONLY`, `FTP_TLS_ONLY`, `SSH_RESTRICTED`,
`SSH_STANDARD_USER`. Le plan peut autoriser un mode ; la security floor
peut le restreindre.

## SSH

Clés publiques gérées comme ressources : fingerprint, algorithm,
comment, status, last_used. Pas de mot de passe SSH si le profil de
sécurité l'interdit. Root login et sudo ne sont jamais des entitlements
client.

## SFTP

Racine et permissions dérivées du managed account root. L'utilisateur ne
choisit ni chroot root ni UID/GID.

## FTP

Optionnel et adapter-based. FTP clair interdit ; FTPS uniquement si le
backend certifié est activé. SFTP reste le choix recommandé.

## Anti-lockout

Toute mutation SSH serveur critique suit validate → second-session
safety check/rollback window → reload → verification. Les changements
host-wide sont WCP-ADMIN only.
