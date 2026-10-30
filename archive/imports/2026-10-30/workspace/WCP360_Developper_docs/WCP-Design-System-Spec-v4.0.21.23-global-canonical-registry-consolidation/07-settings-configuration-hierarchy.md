# 07 --- Hiérarchie de configuration

## Objectif

Empêcher la confusion entre valeur globale, héritée et surcharge locale.

## Pattern V2

Une configuration affiche : - valeur effective ; - origine : default /
global / package / reseller / account / resource ; - surcharge locale
éventuelle ; - possibilité de revenir à l'héritage ; - impact estimé ; -
redémarrage/reload requis ou non.

## États

`inherited`, `overridden`, `pending_apply`, `applied`, `failed`,
`drifted`.

## Diff

Toute modification complexe de configuration doit, lorsque techniquement
possible, présenter un diff avant application et un diff effectif après
application.

## Anti-pattern

Ne jamais afficher un toggle « activé » sans préciser s'il est
réellement effectif ou simplement hérité.
