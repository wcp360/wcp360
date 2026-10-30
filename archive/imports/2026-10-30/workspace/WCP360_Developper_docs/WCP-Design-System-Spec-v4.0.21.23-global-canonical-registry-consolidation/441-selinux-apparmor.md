# 441 --- SELinux / AppArmor

## Objectif

Afficher et, lorsque l'adaptateur le permet, administrer les mécanismes
Mandatory Access Control.

## Pages

Engine status; Mode; Profiles/Policies summary; Denials;
Exceptions/workflows.

## Règle

SELinux et AppArmor sont mutuellement indépendants ; aucune traduction
artificielle de policy entre eux.

## Sécurité

Passage global en permissive/disabled est une action à très haut risque,
capability séparée et confirmation renforcée.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.

## V4.0.3 normative deepening

Voir `SYSTEM-403-15-SELINUX-APPARMOR-MAC.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
