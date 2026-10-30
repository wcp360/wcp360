# 20 --- Boutons & actions

## Objectif

Normaliser toutes les actions et leur hiérarchie.

## Règles obligatoires

-   Variantes : primary, secondary, tertiary, ghost, link, danger, icon,
    split.
-   Tailles : sm/md/lg ; hauteur tactile suffisante.
-   Une zone possède au plus une action primaire dominante.
-   Loading bloque le double-submit et conserve la largeur du bouton.
-   Les actions destructives utilisent danger uniquement au moment
    pertinent.
-   Menu `…` pour actions secondaires, jamais pour cacher l'action
    principale.

## Composants / anatomie

Button, IconButton, ButtonGroup, SplitButton, DropdownAction,
CopyButton.

## États à prévoir

default, hover, focus-visible, active, disabled, loading, success
temporaire.

## Exemples WC

Créer un domaine = Primary. Exporter = Secondary. Supprimer
définitivement = Danger dans la confirmation.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
