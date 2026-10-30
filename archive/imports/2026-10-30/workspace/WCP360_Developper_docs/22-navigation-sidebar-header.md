# 22 --- Navigation, Sidebar et Header

## App Shell

Structure commune :

1.  Sidebar principale
2.  Header global
3.  Breadcrumb
4.  En-tête de page
5.  Contenu
6.  Task/Notification Center global

## Header

Doit pouvoir contenir : - sélecteur de contexte ; - recherche
universelle / Command Palette ; - état global ; - tâches en cours ; -
notifications ; - aide ; - sélecteur de langue ; - compte utilisateur.

## Sidebar

-   Icône + libellé ; jamais icône seule pour la navigation principale.
-   Sections regroupées par domaine fonctionnel.
-   Groupes repliables.
-   État actif sans dépendre uniquement de la couleur.
-   Mode compact autorisé sur desktop.
-   Drawer sur mobile.
-   Contenu filtré selon rôle, permissions et contexte.
-   Ne pas utiliser une liste plate de dizaines de modules.

## WCP-USER

Groupes fonctionnels issus de la base projet : Email, Domains, Files,
Databases, Metrics, Security, Software, Advanced, Preferences,
Applications & Plugins.

## WCP-ADMIN

La navigation doit accueillir Core System, Security & Access, Networking
& DNS, Service Stack, Accounts & Hosting, Email & Collaboration, Backup
& Disaster Recovery, Monitoring & Logs, Packages & Extensions, Developer
& API, System Utilities, Cluster, Storage, SDN, Compute et AI.
