# 37 --- Secrets, credentials et données sensibles

## Composants

SecretField, CredentialCard, TokenCreateResult, SSHKeyFingerprint,
CertificateFingerprint.

## Règles V2

-   Secret masqué par défaut.
-   Les secrets générés une seule fois ont un écran dédié avec
    avertissement.
-   Copier et télécharger sont des actions distinctes.
-   Rotation ≠ suppression.
-   Fingerprints et derniers caractères peuvent être utilisés pour
    identification sans exposer la valeur.
-   Les captures d'écran, toasts, analytics frontend et logs ne doivent
    pas recevoir les secrets.
-   Timeout de révélation recommandé pour secrets réaffichables.

## États

available, one-time, expired, revoked, rotating, compromised/suspected
lorsque le backend le fournit.
