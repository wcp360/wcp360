# 372 --- Integrated Webmail

## Origine

**\[V3.2\]** Webmail intégré Roundcube / SnappyMail.

## Intégrations

Roundcube et SnappyMail sont des adaptateurs. WCP peut exposer un bouton
`Open Webmail` depuis domaine/boîte mail.

## SSO

Si une intégration SSO sûre est disponible : session courte, mailbox
scope, aucune credential dans URL/log. Sinon WCP ne prétend pas offrir
du SSO.

## WCP-ADMIN

Choix du webmail disponible, URL/base integration, politiques, health.

## WCP-USER

Ouverture webmail pour les boîtes autorisées, sans visibilité sur les
autres comptes.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.
