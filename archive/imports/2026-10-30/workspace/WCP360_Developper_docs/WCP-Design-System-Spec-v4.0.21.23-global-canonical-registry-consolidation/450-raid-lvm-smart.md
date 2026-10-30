# 450 --- RAID / LVM / SMART

## Objectif

Approfondir le stockage système local.

## Pages

Block Devices; RAID; LVM PV/VG/LV; SMART/Device Health; Operations.

## Actions

La majorité des mutations sont à haut risque et ne sont exposées que si
un workflow dédié, preflight et récupération sont implémentés.

## Règle

Le monitoring peut être disponible même lorsque les mutations sont
volontairement read-only.

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

Voir `SYSTEM-403-12-DISK-LVM-RAID-SMART.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
