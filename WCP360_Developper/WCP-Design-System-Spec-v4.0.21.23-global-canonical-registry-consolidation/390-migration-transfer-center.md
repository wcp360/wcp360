# 390 --- Migration & Transfer Center

## Origine

**\[V3.3\]** Lacune majeure identifiée : migration d'hébergement entre
panels/serveurs.

## Objectif

Migrer comptes, domaines, fichiers, bases, mail, DNS, SSL, cron et
paramètres avec preflight, mapping, jobs et validation.

## Workflow

Source connection/import artifact → discovery → compatibility/preflight
→ mapping → capacity check → transfer → incremental/final sync si
supporté → cutover checklist → validation → report.

## Pages

Sources; Migrations; Preflight; Mapping; Transfer Jobs; Validation;
Reports; Credentials.

## Sécurité

Credentials source chiffrés/éphémères selon policy ; aucun mot de passe
en logs ; migration cross-tenant strictement autorisée.

## Résultat

Rapport par ressource : migrated, skipped, warning, failed, manual
action required.

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

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
