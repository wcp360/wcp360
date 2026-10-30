# 665 --- Dependency Security & SBOM

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Lockfiles

`composer.lock`, `go.sum` et lockfile JavaScript choisi sont
obligatoires pour les builds reproductibles.

## CI

Inventaire/SBOM, vulnerability scan, secret scan, politique licences et
review des mises à jour.

## MUST

Les dépendances de production proviennent du pipeline de build/release,
pas d'une installation dynamique déclenchée par une requête Web.

## V4.0.7 normative deepening

- `SECOPS-407-26-SECURITY-UPDATES-CVE-SBOM.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.8 normative deepening

- `DEV-408-26-SUPPLY-CHAIN-SBOM.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.
