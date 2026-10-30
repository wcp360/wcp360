# 443 --- CrowdSec / Intrusion Prevention Adapter

## Objectif

Ajouter CrowdSec comme adaptateur optionnel en complément de Fail2ban.

## Pages

Engine; Decisions; Scenarios; Bouncers/integrations if supported;
Events.

## Règle

Ne pas fusionner artificiellement le modèle CrowdSec avec Fail2ban ;
résumé commun, détails spécifiques.

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
