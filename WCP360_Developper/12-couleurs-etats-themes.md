# 12 --- Couleurs, états & thèmes

## Objectif

Définir les couleurs fonctionnelles et Light/Dark/System avec contraste
stable.

## Règles obligatoires

-   États : neutral, info, success, warning, danger, critical.
-   Danger est réservé aux conséquences négatives/destructives.
-   Warning n'est pas utilisé pour une simple décoration.
-   Light et Dark doivent préserver hiérarchie et contrastes ; System
    suit l'OS.
-   Focus ring est distinct de l'accent de marque.

## Composants / anatomie

StatusBadge, Alert, SeverityIndicator, HealthDot, FocusRing,
ThemeSwitcher.

## États à prévoir

Running, stopped, degraded, pending, queued, installing, updating,
suspended, failed, expired, disabled, maintenance, unknown.

## Accessibilité

Texte + icône + couleur pour les états. Contrastes conformes WCAG 2.2
AA.

## Anti-patterns

Pas de "vert = cliquable", pas de texte gris trop faible, pas de rouge
pour une action normale.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
