# 10 --- Design Tokens

## Objectif

Créer une couche de tokens indépendante du framework afin de garantir
cohérence, thèmes, branding revendeur et maintenance.

## Règles obligatoires

-   Aucun composant métier ne code directement une couleur hexadécimale,
    une ombre ou un rayon.
-   Trois niveaux : primitives → sémantiques → composants.
-   Base spacing 4 px.
-   Les tokens de statut gardent la même signification partout.
-   Le branding revendeur peut modifier marque/accent mais pas les
    couleurs de danger/succès au point d'en changer le sens.

## Composants / anatomie

Catégories : color, typography, spacing, size, radius, border, shadow,
opacity, z-index, motion, breakpoint, icon, density.

## Exemples WC

``` yaml
spacing: {1: 4, 2: 8, 3: 12, 4: 16, 6: 24, 8: 32, 12: 48, 16: 64}
radius: {sm: 4, md: 8, lg: 12, pill: 999}
semantic:
  surface: {canvas: ..., panel: ..., elevated: ...}
  text: {primary: ..., secondary: ..., muted: ..., inverse: ...}
  status: {success: ..., warning: ..., danger: ..., info: ...}
```

## Anti-patterns

Pas de `margin: 13px` arbitraire, pas de `#ff0000` dans un composant,
pas de token nommé d'après sa couleur si sa fonction est sémantique.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
