# 653 --- File & Directory Naming

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## PHP

Classes/fichiers `PascalCase.php`, méthodes/variables `camelCase`,
constantes `UPPER_SNAKE_CASE`, PSR-4. Pas de préfixes `ctrl_`, `srv_`,
`tpl_`.

## Go

Packages lowercase, noms de fichiers courts et métier (`service.go`,
`validator.go`, `certificate_store.go`), types exportés PascalCase,
symboles privés camelCase.

## Modules

Noms métier stables : `accounts`, `domains`, `dns`, `websites`,
`databases`, `mail`, `certificates`, `backups`, `files`, `security`,
`monitoring`, `network`, `system`.

## MUST NOT

`misc`, `stuff`, `utils2`, `common2`, `functions` comme conteneurs
génériques de logique.
