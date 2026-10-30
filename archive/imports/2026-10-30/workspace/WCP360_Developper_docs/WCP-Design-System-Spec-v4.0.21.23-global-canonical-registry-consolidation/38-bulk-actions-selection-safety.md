# 38 --- Sélection multiple et actions en masse

## Objectif

Éviter les erreurs de portée dans les Data Grids.

## Règles

-   La sélection affiche le nombre exact d'objets.
-   Changer de filtre/page/contexte suit une règle explicite de
    conservation ou d'annulation.
-   Une action bulk indique combien d'objets sont éligibles, ignorés ou
    interdits.
-   Une action destructive en masse affiche un résumé d'impact.
-   Les résultats partiels sont représentés : succès, échec, ignoré.
-   Pour les gros volumes, créer un job Task Center plutôt qu'attendre
    dans la page.

## Anti-pattern

`Select all` ambigu entre page courante et totalité du dataset.
