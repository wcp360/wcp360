# 420 --- Backup Integrity Verification

## Objectif

Ne pas considérer `job succeeded` comme preuve suffisante qu'une
sauvegarde est utilisable.

## Vérifications

Checksum/object existence, manifest completeness, encryption metadata,
destination readability et contrôles spécifiques au backend.

## États

Unverified, verified, verification failed, expired/unavailable.

## UI

Le dashboard distingue dernière sauvegarde créée et dernière sauvegarde
vérifiée.

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

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
