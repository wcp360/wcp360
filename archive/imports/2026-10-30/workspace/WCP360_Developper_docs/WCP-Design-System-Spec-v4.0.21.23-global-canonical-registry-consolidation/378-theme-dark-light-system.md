# 378 --- Theme: Light / Dark / System

## Origine

**\[V3.2\]** Formalise les modes déjà présents dans les foundations.

## Modes

Light, Dark, System. Préférence persistée par utilisateur; `System` suit
`prefers-color-scheme`.

## Tokens

Toutes les surfaces, textes, borders, focus, charts, statuses et code
editor consomment des tokens sémantiques. Aucun module métier ne code
directement ses couleurs.

## Accessibilité

Contraste WCAG 2.2 AA, focus visible, états non transmis par couleur
seule.

## White-label

Le branding revendeur fournit des tokens de marque bornés; les tokens de
sécurité/status et contrastes critiques restent protégés.

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
