# 31 --- Création, édition & wizards

## Objectif

Standardiser les workflows multi-étapes et éviter les formulaires
interminables.

## Règles obligatoires

-   Wizard seulement si dépendances réelles entre étapes.
-   Étapes nommées, progression visible, retour possible sans perte.
-   Résumé final avant création pour ressources coûteuses/critiques.
-   Valeurs par défaut sûres.
-   Advanced settings repliés mais recherchables.

## Composants / anatomie

Stepper, WizardShell, ReviewStep, AdvancedSection, StickyActions.

## États à prévoir

draft, validating, provisioning, success, partial success, failed,
retry.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
