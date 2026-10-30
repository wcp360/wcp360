# 1180 --- CI Pipeline V1

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Stages : validate repository → PHP lint/format/static analysis → Go
fmt/vet/static analysis → unit → DB migration lint/test → OpenAPI
validation → IPC contract tests → security negative tests → integration
PostgreSQL → build artifacts → SBOM → package → VM smoke.

Branch protection exige tous gates applicables. Secrets CI utilisent
secret store du CI, jamais repository variables en clair.
