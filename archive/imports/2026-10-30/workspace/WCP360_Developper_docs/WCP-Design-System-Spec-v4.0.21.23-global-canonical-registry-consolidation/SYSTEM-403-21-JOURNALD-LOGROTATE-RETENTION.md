# V4.0.3 --- journald, Log Files, Rotation & Retention

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Sources

journald et fichiers applicatifs sont des sources distinctes. Chaque
module déclare ses logs et ownership.

## Lecture

Time range, severity, unit/resource, correlation, pagination/cursor. Pas
de lecture illimitée de fichiers root via path utilisateur.

## Redaction

Secrets, auth headers, cookies, passwords, private keys et tokens
doivent être masqués à la production quand possible et filtrés avant
exposition.

## Rotation

WCP-owned file logs possèdent logrotate policy ou équivalent :
size/time, retention, compression, permissions, postrotate borné.

## Disk pressure

Log growth est monitoré. En situation critique, préserver audit/sécurité
selon policy sans laisser les logs remplir le root filesystem.

## Export

SIEM/syslog export est adapter optionnel avec backpressure/failure
policy.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
