# MASTER V4.0.21.4.1 — Commands, Options & Isolation Deepening

## Statut
**NORMATIVE ADDENDUM — NO CODE — REPOSITORY BLOCKED**

## Constat
Le corpus prévoit déjà `659-command-action-registry.md`, cgroups v2, quotas/limits, invariants multi-tenant et isolation PHP. Cette passe les consolide avant B1/B2.

## Décisions
1. Une opération métier canonique, plusieurs surfaces : WCP-ADMIN/WCP-USER UI, REST API, future CLI, billing et automation.
2. Prévoir `wcpctl` comme CLI WCP-ADMIN ; elle n'exécute jamais directement une commande root arbitraire.
3. Linux natif = backend isolation V1 : UID/GID + filesystem boundaries + cgroups v2 + systemd slices/scopes + runtime separation + quotas.
4. CloudLinux/LVE, CageFS, AppArmor, SELinux, seccomp, chroot et sandbox sont des adapters/profils optionnels selon certification.
5. `IsolationProfile` devient la ressource canonique de composition.
6. Une limite doit exposer son état d'enforcement réel : `HARD`, `SOFT`, `UNAVAILABLE`, `INHERITED`, `UNLIMITED_WITHIN_PARENT`.
7. Entitlement commercial, quota mesurable et runtime isolation restent trois concepts distincts.

**NO CODE — REPOSITORY BLOCKED.**
