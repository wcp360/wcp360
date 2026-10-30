# 651 --- Language Policy

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Cœur WCP

  Couche                              Langage
  ----------------------------------- ---------------------------
  WCP-ADMIN / WCP-USER / API Web      PHP
  UI navigateur                       HTML/CSS/JavaScript
  wcpd / agent / wcp-privd / wcpctl   Go
  Bootstrap, packaging, CI            Shell minimal et contrôlé

## MUST NOT

Python, Node.js, Ruby, Perl ou autres runtimes administrés par WCP ne
deviennent pas des dépendances du cœur sans ADR.

## Shell

Interdit pour la logique métier issue de requêtes utilisateurs. Autorisé
pour build/bootstrap/package/maintenance contrôlée, avec validation
stricte et sans interpolation de données non fiables.
