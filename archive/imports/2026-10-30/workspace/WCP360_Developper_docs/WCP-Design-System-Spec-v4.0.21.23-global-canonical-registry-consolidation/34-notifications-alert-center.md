# 34 --- Notifications & Alert Center

## Objectif

Centraliser événements utilisateur et alertes opérationnelles.

## Règles obligatoires

-   Séparer notifications produit et alertes infrastructure.
-   Sévérité, source, ressource, timestamp, statut lu/acquitté.
-   Canaux configurables : in-app, email, SMS/webhook si backend
    disponible.
-   Déduplication/corrélation pour éviter tempête d'alertes.

## Composants / anatomie

NotificationBell, NotificationCenter, AlertRule, AlertDetail,
ChannelPreferences.

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

- `OPS-4010-09-ALERT-RULE-MODEL.md`
- `OPS-4010-10-ALERT-DEDUP-SUPPRESSION.md`
- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
