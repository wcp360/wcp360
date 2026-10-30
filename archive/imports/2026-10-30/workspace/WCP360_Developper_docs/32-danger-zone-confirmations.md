# 32 --- Danger Zone et confirmations

## Niveaux

### Niveau 1 --- Réversible / faible impact

Confirmation légère ou Undo.

### Niveau 2 --- Impact significatif

Dialog explicitant la ressource et les conséquences.

### Niveau 3 --- Destructif / irréversible

Exiger une confirmation renforcée : saisie du nom de la ressource,
réauthentification ou 2FA selon le risque.

## Danger Zone

Regrouper séparément les opérations telles que : - suspendre un compte
; - réinitialiser une configuration ; - supprimer un site ; - supprimer
un compte ; - détruire un serveur ; - supprimer un cluster/nœud lorsque
applicable.

## Confirmation

Toujours préciser : objet ciblé, portée, données impactées, caractère
réversible ou non, éventuelle sauvegarde disponible et durée estimée
lorsque connue.

## Niveaux de risque

-   **R0** réversible : action immédiate + undo si réel.
-   **R1** impact limité : confirmation simple.
-   **R2** impact important : dialog avec conséquences et cible.
-   **R3** destructif/irréversible : saisie du nom/ID de la ressource +
    step-up auth si politique.

## Règles

Le bouton de confirmation nomme l'action (« Supprimer le domaine »),
jamais « OK ». Afficher dépendances touchées, sauvegarde disponible,
délai éventuel et ce qui sera conservé. Une confirmation ne remplace pas
l'autorisation backend.
