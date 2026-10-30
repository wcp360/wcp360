# V4.0.17 — Contract Coverage Audit

## Vérification par rapport au gate V4.0.16
1. Canonical resource/data model — **SPECIFIED**.
2. Logical DB schema — **SPECIFIED**.
3. API endpoint/resource contract map — **SPECIFIED**.
4. Job/state-machine catalog — **SPECIFIED**.
5. `wcpd` RPC method catalog — **SPECIFIED**.
6. Package/filesystem ownership tree — **SPECIFIED**.
7. systemd unit specification — **SPECIFIED**.
8. Configuration generation contracts — **SPECIFIED**.
9. Error taxonomy — **SPECIFIED**.
10. Security acceptance tests — **SPECIFIED**.
11. E2E acceptance matrix — **SPECIFIED**.
12. Migration/versioning plan — **SPECIFIED**.

## Extensions nécessaires ajoutées
RBAC capability contract, secret references, events/outbox/webhooks, audit record, observed-state reconciliation, UI async-state contract, naming/identifier rules, nonfunctional limits and implementation traceability.

## Limite
`SPECIFIED` ne signifie pas encore `READY FOR CODE`; la cohérence cross-corpus et la traçabilité doivent être auditées avant ouverture du repository gate.
