# 493 --- NAT / Advanced Routing / DHCP

## Scope

Module WCP-ADMIN optionnel pour infrastructures nécessitant routage/NAT.
DHCP n'est pas requis pour un serveur d'hébergement classique.

## Fonctions

SNAT/DNAT/masquerade, routes statiques, policy routing si backend
supporté, forwarding IPv4/IPv6, DHCP server adapter optionnel.

## Sécurité

Les règles NAT utilisent le même moteur de transaction/firewall que le
backend réseau. Validation de conflits et de ports avant apply.

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
