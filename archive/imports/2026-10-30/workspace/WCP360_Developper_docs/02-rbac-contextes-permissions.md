# 02 --- RBAC, contextes et permissions

## Rôles fonctionnels de base

-   Client
-   Revendeur
-   Admin
-   Super Admin

## Modèle recommandé

Ne pas limiter l'autorisation au rôle. Toute autorisation doit pouvoir
être décrite par :

`subject + role + scope + resource + action + conditions`

Exemple :

``` yaml
role: server_admin
scope: srv-paris-03
permissions:
  - server.read
  - service.read
  - service.restart
  - logs.read
denied:
  - server.destroy
  - account.delete
```

## Context switcher

Le header doit permettre, selon les droits, de changer de contexte :
organisation, serveur, compte, abonnement, domaine ou projet.

Le contexte actif doit toujours être visible sur les écrans où une
confusion pourrait provoquer une opération sur la mauvaise ressource.

## Impersonation

Toute impersonation doit afficher un bandeau persistant avec :
utilisateur impersonné, initiateur, heure de début et action « Quitter
l'impersonation ». Toutes les opérations restent auditées.
