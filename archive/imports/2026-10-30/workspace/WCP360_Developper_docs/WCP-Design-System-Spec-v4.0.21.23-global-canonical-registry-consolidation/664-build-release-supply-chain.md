# 664 --- Build, Release & Supply Chain

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Pipeline

Source → review → tests → SAST/dependency/secret scan → build → SBOM →
artifact versionné → checksum/signature → repository approuvé →
`wcp-update` → release immutable → health check.

## MUST NOT

`git pull`, build improvisé, `composer update`, téléchargement de
dépendances ou auto-modification du code depuis l'application Web en
production.

## Provenance

Une release possède version, provenance, intégrité et procédure de
rollback compatible.

## V4.0.12 normative deepening

- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-02-RELEASE-MANIFEST-ARTIFACTS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
