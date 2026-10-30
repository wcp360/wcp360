# 440 --- Host Security Manager

## Objectif

Vue de posture hôte réunissant firewall, SSH, updates, MAC framework,
intrusion prevention, malware et findings.

## Pages

Posture; Findings; Firewall; SSH; MAC; Updates/CVE; Intrusion
Prevention; Malware; Exceptions.

## Règle

Le Security Center agrège mais ne remplace pas les modules spécialisés.
Chaque finding conserve evidence/source/timestamp.

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
