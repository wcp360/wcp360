# 23 --- Cards, badges & statuts

## Objectif

Normaliser les surfaces de synthèse sans transformer le dashboard en
mosaïque décorative.

## Règles obligatoires

-   Card pour regrouper un sujet, pas comme conteneur universel.
-   Badge = métadonnée courte ; StatusBadge = état sémantique.
-   KPI card affiche valeur, unité, période et tendance/source si utile.
-   Health card doit distinguer état actuel et historique.

## Composants / anatomie

Card, StatCard, ResourceCard, StatusBadge, CountBadge, Tag,
HealthIndicator, ServiceRow.

## États à prévoir

normal, warning, critical, unknown, stale data, loading.

## Anti-patterns

Éviter 20 cartes identiques, badges multicolores décoratifs, KPI sans
période ou unité.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.

## V4.0.10 normative deepening

- `OPS-4010-18-STATUS-COMPONENTS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
