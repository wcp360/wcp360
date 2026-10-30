# 190 --- SSH Access & Keys

> **V3.5 — rôle du document : overview des accès.** La configuration serveur canonique est `550-ssh-server-manager.md`; clés et restrictions sont détaillées dans `551–554`.


## Statut et origine

\[SOURCE\] SSH Manager, root/sudo policies, SSH keys.

## Objectif

Gérer accès SSH côté WCP-ADMIN.

## Pages / sous-vues

Service; Policies; Keys; Sessions if available; Known authorized
principals/users.

## Ressources et données

state, port/config fields allowed, key fingerprints, users.

## Actions opérateur

Add/revoke key; approved policy changes; restart/reload service.

## Monitoring, santé et logs

Auth failures/sessions if safely available.

## Sécurité, permissions et audit

Never expose private keys; lockout prevention; step-up for critical
changes.

## Contrat backend attendu

SSH adapter; structured policy; fingerprint-based key management.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Ajout de clé ne permet jamais l'upload d'une clé privée comme clé
autorisée.

## V4.0.7 normative deepening

- `SECOPS-407-10-SSH-USER-KEYS-SHELL.md`
- `SECOPS-407-11-SSH-SERVER-ANTI-LOCKOUT.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
