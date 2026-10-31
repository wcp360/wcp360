# 495 --- VPN / WireGuard Adapter

## Positionnement

Fonction optionnelle P2, non nécessaire au cœur hosting.

## Scope

Interfaces/tunnels WireGuard, peers, allowed IPs, endpoint,
handshake/health lorsque disponible. OpenVPN ou autres solutions
pourront utiliser d'autres adapters.

## Sécurité

Private keys write-only ; QR/export uniquement avec permission explicite
; aucune clé dans audit/log.

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
