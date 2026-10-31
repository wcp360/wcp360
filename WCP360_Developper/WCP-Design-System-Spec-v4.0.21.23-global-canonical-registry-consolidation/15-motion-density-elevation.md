# 15 --- Motion, densité et élévation

## Motion

Animations fonctionnelles uniquement : apparition de feedback,
expansion, changement d'état. Respecter `prefers-reduced-motion`. Aucune
animation ne doit retarder une action urgente.

## Densité

Trois densités possibles pour Data Grid et vues techniques :
Comfortable, Default, Compact. La densité ne réduit jamais les cibles
tactiles sous le minimum accessible retenu.

## Élévation

Limiter les niveaux : base, sticky, dropdown/popover, drawer, modal,
critical overlay. Les z-index sont des tokens et non des nombres
arbitraires.

## Skeleton

Le skeleton reflète la géométrie réelle et ne simule pas des données qui
pourraient être interprétées comme vraies.
