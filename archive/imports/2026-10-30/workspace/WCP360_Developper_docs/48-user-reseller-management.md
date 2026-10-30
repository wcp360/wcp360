# 48 --- WCP-USER : expérience Revendeur

## Origine

\[SOURCE\] Le revendeur dispose d'accès supplémentaires : comptes,
quotas, branding et applications.

## Architecture V2

Le revendeur reste dans WCP-USER ; il ne bascule pas dans WCP-ADMIN.

## Pages

-   Comptes clients
-   Création/édition de compte
-   Quotas et consommation
-   Packages autorisés si exposés par l'admin
-   Branding revendeur
-   Applications/fonctions disponibles
-   Activité et alertes de son scope

## Règles

Le Context Switcher distingue
`Revendeur → Client → Hébergement/Domaine`. Toute impersonation client
est explicitement signalée et auditée.

## Sécurité

Aucun accès aux ressources serveur globales, aux autres revendeurs ou
aux capacités root.
