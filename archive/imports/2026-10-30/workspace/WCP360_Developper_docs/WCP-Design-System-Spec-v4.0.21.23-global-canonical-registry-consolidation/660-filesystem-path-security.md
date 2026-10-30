# 660 --- Filesystem & Path Security

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Principe

Les API publiques utilisent des resource IDs, pas des chemins Linux. Un
`PathResolver` central traduit compte/domaine/ressource vers le chemin
autorisé.

## MUST

Confinement dans une racine attendue, contrôle ownership/type,
protection symlink, opérations résistantes aux races TOCTOU lorsque la
sécurité en dépend, tailles/quotas et permissions minimales.

## MUST NOT

Concaténer `/home/` avec une entrée utilisateur, accepter `..`, chemin
absolu arbitraire, ou suivre silencieusement un symlink hors scope.

## V4.0.7 normative deepening

- `SECOPS-407-01-FILE-ROOT-PATH-RESOLUTION.md`
- `SECOPS-407-08-SYMLINK-HARDLINK-MOUNT-BOUNDARIES.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
