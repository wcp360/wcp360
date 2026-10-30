# 376 --- Reseller White-label / Branding

## Origine

**\[V3.2\]** Marque blanche revendeur.

## Pages

Branding; Logo; Favicon; Product Name; Access Domain; Theme Tokens;
Login Branding; Preview.

## Personnalisation

Logo/favicons, nom commercial, domaine d'accès si infrastructure/TLS/DNS
validés, couleurs via **tokens sémantiques contrôlés**. Pas d'injection
CSS arbitraire par défaut.

## Héritage

Platform brand → reseller brand → user experience. L'administrateur peut
verrouiller certains éléments.

## Accessibilité

Le système recalcule/valide contraste des combinaisons critiques et
refuse ou avertit selon politique.

## Sécurité

Uploads image validés; custom domain passe par vérification
ownership/DNS/TLS.

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
