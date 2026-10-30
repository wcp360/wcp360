# 27 --- Charts, métriques & ressources

## Objectif

Standardiser monitoring, quotas et capacité.

## Règles obligatoires

-   Chaque graphique affiche unité, plage temporelle et fraîcheur des
    données.
-   Ne pas tronquer l'axe de manière trompeuse.
-   Seuils warning/critical documentés.
-   Valeur actuelle, moyenne et pic pour métriques opérationnelles quand
    pertinent.
-   Quotas distinguent usage, limite et illimité.

## Composants / anatomie

TimeSeriesChart, Sparkline, Gauge, ResourceBar, QuotaMeter, Legend,
TimeRangePicker.

## États à prévoir

live, delayed, stale, no data, partial data, threshold exceeded.

## Accessibilité

Résumé textuel des graphiques et table de données accessible pour
informations essentielles.

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

- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-04-PROMETHEUS-ADAPTER.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
