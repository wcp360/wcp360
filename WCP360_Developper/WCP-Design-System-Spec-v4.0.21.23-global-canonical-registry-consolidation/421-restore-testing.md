# 421 --- Restore Testing

## Objectif

Tester périodiquement la restaurabilité dans un environnement isolé
lorsque l'infrastructure le permet.

## Workflow

Select recovery point → isolated target → restore → health/integrity
checks → destroy test target → report.

## Sécurité

Aucune collision avec production ; secrets et données restaurées soumis
aux mêmes contrôles ; destruction de l'environnement test auditée.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.

## V4.0.7 normative deepening

- `SECOPS-407-17-BACKUP-INTEGRITY-VERIFICATION.md`
- `SECOPS-407-19-DISASTER-RECOVERY.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-26-CONTROL-PLANE-RESTORE.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
