# 658 --- Privileged Broker Security

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Principe

`wcp-privd` est le plus petit composant root du projet : aucune UI,
session Web, template, logique métier générale ou interpréteur de
commandes.

## Requête

Action ID fermée + paramètres typés + resource context. Le broker
revalide les contraintes pertinentes ; il ne fait pas confiance à PHP ni
aveuglément à wcpd.

## MUST NOT

`command`, `shell`, `exec`, nom de binaire libre, unité systemd libre,
chemin arbitraire ou argument opaque fourni par l'utilisateur.

## Review

Toute nouvelle action root nécessite security review et tests négatifs.
