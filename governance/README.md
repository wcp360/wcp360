# WCP360 Governance

This repository is the versioned project authority for approved WCP360 artifacts.

## Authority model

Owner discussions remain authoritative for their domains:

- MASTER / Project Control — coordination, baselines, readiness, conflicts.
- Documentation projet détaillée — functional/documentary authority.
- Design System — UI/UX authority.
- MODULES — module catalogue.
- API / IPC / CONTRACTS — API, IPC, jobs, events, errors and compatibility.
- DATABASE / DATA MODEL — PostgreSQL model and migrations.
- SECURITY / HARDENING — security requirements and privileged boundaries.
- TESTS / QA — test strategy and validation.
- DEVOPS / BUILD / RELEASE — platform, install, build and release.
- ENGINEERING / CODE REVIEW — engineering rules and independent code review.
- PERFORMANCE / SCALABILITY — capacity and performance.
- OBSERVABILITY / INCIDENTS / SRE — observability and operational reliability.
- TECHNICAL R&D / ARCHITECTURE LAB — research and proposals only.

## Change rule

No domain may silently redefine another owner's approved contract.

Cross-domain impact requires a **WCP360 HANDOFF**.

Contradiction requires a **WCP360 CONFLICT** to MASTER and, when needed, AUDIT / ARCHITECTURE REVIEW.

## Traceability target

```text
Requirement
→ Module
→ Architecture
→ Security
→ Data Model
→ API / IPC Contract
→ UI / UX
→ Observability
→ Tests
→ Implementation
→ Code Review
→ Performance Validation
→ Build / Release
→ Production Observability
→ Incident / Bug
→ Fix
→ Regression Test
→ Documentation
```

## Repository status

```text
WCP360-BM-0001 = FINAL
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
