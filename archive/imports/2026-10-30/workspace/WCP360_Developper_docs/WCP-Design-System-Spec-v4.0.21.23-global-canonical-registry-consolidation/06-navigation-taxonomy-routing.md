# 06 --- Navigation, routes et adressabilité

## Route contract V2

Chaque page métier importante possède une route stable et partageable.
Les paramètres sensibles ne sont pas placés dans l'URL.

## Hiérarchie

-   Niveau 1 : domaine fonctionnel.
-   Niveau 2 : liste ou vue principale.
-   Niveau 3 : page ressource et tabs. Éviter plus de deux niveaux
    persistants dans la sidebar.

## Navigation globale

Logo/produit, Context Switcher, recherche/Command Palette, Task Center,
Notifications, aide, profil.

## WCP-USER

Navigation orientée tâches : Email, Domaines, Fichiers, Bases,
Métriques, Sécurité, Logiciels, Avancé, Préférences, Applications.

## WCP-ADMIN

Navigation orientée exploitation : Core, Sécurité, Réseau/DNS, Services,
Comptes/Hébergement, Email, Backup/DR, Monitoring, Packages,
Developer/API, System Utilities, puis Cluster/Storage/SDN/Compute/AI
selon capacités.

## Deep links

Une alerte, un job, un audit event et un résultat de recherche doivent
pouvoir pointer vers la ressource et, si possible, la sous-vue
pertinente.
