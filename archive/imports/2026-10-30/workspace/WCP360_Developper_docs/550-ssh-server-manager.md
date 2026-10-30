# 550 --- SSH Server Manager

## Objectif

Séparer configuration du serveur SSH des accès utilisateurs.

## Pages

Daemon; Listening; Authentication; Root Policy; Host Keys; Allowed
Users/Groups; Sessions; Logs; Changes.

## Paramètres

Ports/listen addresses, password auth policy, public key auth, root
login, idle/session controls lorsque supportés, SFTP subsystem.

## Anti-lockout

Validate config before reload ; conserver un chemin de récupération ;
changement de port/auth à confirmation renforcée.

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
