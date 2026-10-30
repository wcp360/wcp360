# 1071 --- CI Quality Gates

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Gates : formatting/lint, unit, static analysis, dependency audit, secret
scan, migrations validation, OpenAPI validation, Go/PHP tests, security
tests, build reproducibility checks, SBOM generation.

Main/release protégées ; artefact produit par CI, jamais construit par
l'UI de production.
