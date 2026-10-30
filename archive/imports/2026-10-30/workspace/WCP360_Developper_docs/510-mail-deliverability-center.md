# 510 --- Mail Deliverability Center

## Objectif

Vue unifiée de la santé de délivrabilité par domaine/IP.

## Signaux

SPF, DKIM, DMARC, reverse DNS/PTR, TLS, MTA-STS/TLS-RPT capability,
queue/rejects, reputation observations, blocklists configurées, sending
anomalies.

## UI

Score éventuel uniquement comme synthèse explicable ; chaque finding
conserve evidence, source et timestamp.

## Actions

Open DNS fix, rotate DKIM, inspect queue/logs, configure policy, run
recheck.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.
