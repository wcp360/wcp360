# 551 --- User SSH Keys

## Pages

Keys; Add/Import; Generate if product permits; Assignments; Expiry
metadata; Last use if observable.

## Données

Type, fingerprint, comment, owner, scope, created, expiration policy,
status.

## Sécurité

Private key générée côté WCP n'est affichée/téléchargeable qu'une fois
si cette fonction est retenue ; préférence à l'import de clé publique.

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
