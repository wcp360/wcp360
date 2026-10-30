# 502 --- AXFR / IXFR / TSIG

## Fonctions

Zone transfer policies, AXFR/IXFR capability, ACL, TSIG keys/references,
NOTIFY targets.

## Secrets

TSIG secret write-only ; UI expose nom, algorithm supporté,
fingerprint/metadata, rotation et assignments.

## Audit

Création/rotation/suppression de clé et changement d'ACL sont des
événements sensibles.

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
