# 11 --- Typographie & iconographie

## Objectif

Normaliser la lisibilité d'une interface technique dense sans sacrifier
la hiérarchie.

## Règles obligatoires

-   Police UI sans-serif, pile système ou famille variable performante.
-   Monospace réservé aux IP, domaines techniques, chemins, commandes,
    logs, clés et code.
-   Échelle recommandée : 12/14/16/18/20/24/30/36 px ; line-height
    1.3--1.6 selon usage.
-   H1 unique par page. Les cartes ne doivent pas simuler des H1.
-   Icônes d'une seule famille ; 16/20/24 px ; trait cohérent.
-   Une icône seule exige tooltip et nom accessible.

## Composants / anatomie

Text, Heading, CodeText, Kbd, Icon, IconButton, CopyableValue,
TruncatedValue.

## Accessibilité

Ne jamais utiliser une icône seule pour communiquer succès/erreur.
Autoriser agrandissement texte et éviter les textes \<12 px.

## Exemples WC

`srv-paris-01`, `192.0.2.10`, `/var/www/example`,
`systemctl restart nginx` utilisent le style monospace.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
