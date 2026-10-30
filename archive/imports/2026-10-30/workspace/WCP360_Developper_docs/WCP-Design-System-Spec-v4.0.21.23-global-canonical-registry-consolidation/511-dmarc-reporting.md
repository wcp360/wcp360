# 511 --- DMARC Reporting

## Scope

Ingestion/connexion à une source de rapports agrégés si configurée ; WCP
n'invente pas de données DMARC sans rapports reçus.

## Pages

Domains; Alignment; Sources; Failures; Trend; Report ingestion status.

## Privacy

Les rapports peuvent contenir des données opérationnelles sensibles ;
rétention et accès sont gouvernés.

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

## V4.0.6 normative deepening

- `MAIL-406-13-DMARC-DEEP.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.
