# V4.0.15 --- PARTIAL / Decision / TODO Re-check

## Comptage brut

  Marqueur                Occurrences   Fichiers
  --------------------- ------------- ----------
  `PARTIAL`                       613        208
  `DECISION REQUIRED`              28         16
  `TODO`                           13         10
  `TBD`                            14         10
  `FIXME`                           0          0
  `PLACEHOLDER`                     1          1

## Interprétation

Comme établi en V4.0.13, `PARTIAL` est aussi un terme métier légitime.
Les anciens audits restent conservés et peuvent contenir des décisions
historiques.

## Règle de gel

À partir de V4.0.15 : - un nouveau `DECISION REQUIRED` de nature
architecture devient un **ADR** ; - un `TODO` fonctionnel doit être
classé CORE/OPTIONAL/EXPERIMENTAL/FUTURE ; - aucun marqueur n'est
supprimé mécaniquement pour fabriquer un faux état READY.
