# 500 --- Authoritative DNS Architecture

## Objectif

Séparer le modèle DNS WCP du moteur authoritative.

## Contrat

Zones, records, SOA, serial, validation, DNSSEC capability, transfer
capability, notify capability, health.

## Adapters

BIND est déjà couvert ; l'architecture autorise ultérieurement
PowerDNS/Knot/NSD sans changer le modèle WCP-USER.

## Règle

Recursive resolver et authoritative DNS sont des responsabilités
distinctes.

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
