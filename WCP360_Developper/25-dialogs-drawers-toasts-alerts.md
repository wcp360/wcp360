# 25 --- Dialogs, drawers, toasts & alerts

## Objectif

Choisir le bon niveau d'interruption selon importance et risque.

## Règles obligatoires

-   Toast : confirmation non bloquante et temporaire.
-   Alert inline : information liée à la page.
-   Dialog : décision courte bloquante.
-   Drawer : édition/détails secondaires conservant le contexte.
-   Full-screen dialog : workflows complexes sur mobile.
-   Une erreur critique persistante ne doit pas disparaître comme un
    toast.

## Composants / anatomie

Toast, InlineAlert, Banner, Dialog, ConfirmDialog, Drawer, Popover.

## États à prévoir

info, success, warning, danger ; loading ; retry ; details expandable.

## Accessibilité

Focus trap dans dialog, retour du focus au déclencheur, Escape sauf
opération non annulable, live region pour toasts.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
