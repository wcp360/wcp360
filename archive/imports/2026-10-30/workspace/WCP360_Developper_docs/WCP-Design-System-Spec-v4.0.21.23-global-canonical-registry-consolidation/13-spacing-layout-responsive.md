# 13 --- Spacing, layout & responsive

## Objectif

Établir la grille, les densités et comportements responsive d'un panneau
desktop-first.

## Règles obligatoires

-   Grille 12 colonnes pour les dashboards ; contenu standard max \~1440
    px, vues techniques pouvant utiliser toute la largeur.
-   Espacement basé sur 4 px.
-   Header global \~56--64 px ; sidebar \~240--280 px, compact \~64--72
    px.
-   Densité Comfortable par défaut WCP-USER ; Compact disponible
    WCP-ADMIN/data grids.
-   Aucun scroll horizontal de page causé par le layout ; seules les
    zones techniques peuvent scroller localement.

## Composants / anatomie

Container, Stack, Inline, Grid, SplitPane, ResizablePanel,
StickyActionBar.

## Responsive

≥1440 large desktop ; 1024--1439 desktop ; 768--1023 tablette ; \<768
mobile. Les tables passent à colonnes prioritaires + détails, pas en
cartes arbitraires pour les datasets complexes.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
