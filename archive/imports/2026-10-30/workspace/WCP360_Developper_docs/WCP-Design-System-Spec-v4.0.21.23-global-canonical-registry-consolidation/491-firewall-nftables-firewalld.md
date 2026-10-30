# 491 --- nftables / firewalld Firewall Adapters

## Objectif

Donner un backend concret au modèle Firewall existant.

## Adapters

nftables et firewalld sont indépendants. L'interface commune couvre
zones/rules/services/ports/sources lorsque le backend sait les
représenter ; les fonctions spécifiques restent dans une vue avancée.

## Règles

Détection du backend actif, validation avant apply, diff, protection du
port/session d'administration, rollback/recovery lorsque disponible.

## Interdit

Ne pas gérer simultanément nftables brut et firewalld comme deux sources
de vérité sans stratégie explicite.

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

## V4.0.3 normative deepening

Voir `SYSTEM-403-16-FIREWALL-NFTABLES-FIREWALLD.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.

## V4.0.14 normative deepening

- `BOOT-4014-08-FIREWALL-PORT-PLAN.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
