# ADR-018 --- Authentication, Sessions, MFA & Recovery

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

Comptes WCP natifs V1 avec password hashing moderne via bibliothèque
maintenue, MFA **TOTP** CORE et recovery codes à usage unique.
WebAuthn/passkeys devient P1 sans bloquer l'architecture.

## Session

Cookie Secure + HttpOnly + SameSite approprié, session server-side
révocable, rotation après auth/privilege change, idle + absolute
timeout.

## CSRF

Protection explicite pour browser mutations ; API tokens ne reposent pas
sur cookies.

## API

Personal/service tokens scoped, hashés au repos,
expiration/rotation/revocation et last-used metadata.

## Step-up

Actions sensibles : impersonation, recovery, secret exposure/rotation,
destructive operations, security policy changes.

## Recovery

Admin lockout suit V4.0.14 ; reset ne contourne pas audit/notification.
