# 05 --- Modèle de menace appliqué à l'UI

## Objectif

Traiter le panneau comme une interface d'administration sensible.

## Menaces V2 à considérer

Confusion de scope, CSRF selon mode d'authentification, XSS dans
logs/noms/metadata, fuite de secrets, clickjacking, élévation de
privilèges, replay/double-submit, IDOR, commandes injectées, uploads
dangereux, liens externes trompeurs.

## Règles UI

-   Toujours afficher la cible exacte avant une mutation sensible.
-   Encoder le contenu non fiable, notamment logs et sorties terminal.
-   Les valeurs secrètes sont masquées et ne sont révélées qu'à la
    demande.
-   `Copy secret` n'écrit pas le secret dans les notifications ou logs.
-   Les actions critiques ne reposent jamais sur un simple toggle
    ambigu.
-   Les changements de scope invalident les sélections/bulk actions
    incompatibles.
-   Les formulaires de commandes/configurations distinguent texte, code
    et valeurs secrètes.

## Règles backend associées

L'UI ne remplace jamais : autorisation objet, validation, rate limiting,
journalisation, protection de session, isolation tenant, contrôle des
chemins de fichiers et sandbox d'exécution.
