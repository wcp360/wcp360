# 156 --- Anti-spam, Greylisting & Relay

## Statut et origine

\[SOURCE\] Anti-spam, greylisting, relay settings.

## Objectif

Configurer protections mail selon stack installée.

## Pages / sous-vues

Policies; scores; allow/deny; greylisting; relay.

## Ressources et données

effective policy, origin/inheritance, exceptions.

## Actions opérateur

Edit policy; add exception; test if backend supports.

## Monitoring, santé et logs

Spam counts only if measured.

## Sécurité, permissions et audit

Relay changes high-risk; audit and validation.

## Contrat backend attendu

Capability-driven schema.

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

Le module indique clairement la couche qui applique chaque règle.

## V4.0.6 normative deepening

- `MAIL-406-15-RSPAMD-ANTISPAM-DEEP.md`
- `MAIL-406-16-CLAMAV-MAIL-ANTIVIRUS.md`
- `MAIL-406-19-OUTBOUND-RATE-LIMITS-ABUSE.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`
- `BOOT-4014-14-MAIL-ABUSE-REPUTATION.md`
- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
