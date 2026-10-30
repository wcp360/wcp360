# V4.0.21.3 --- Deterministic Gap Closure Order

## Ordre obligatoire

1.  **Data / Resource contracts** --- champs, types, invariants,
    ownership.
2.  **API operation registry** --- une ligne par action CORE.
3.  **Authorization** --- capability, scope, step-up et
    ceilings/entitlements.
4.  **Execution contract** --- explicit no-job ou Job exact.
5.  **Privileged boundary** --- RPC/adapters exacts et Decision IDs.
6.  **Audit/Event** --- action et payload sûr.
7.  **Delete/Retention** --- disposition explicite par ressource.
8.  **Acceptance** --- Operation ID → Job → Decision/RPC/Adapter →
    oracle.
9.  **Cross-family dependency audit**.
10. **Global re-audit**.

## Interdictions

Pas de wildcard d'action, pas de `target adapter`, pas de `same as`, pas
de policy sans ID, pas de cascade implicite, pas de protection
uniquement frontend, pas de suppression automatique pour satisfaire un
quota.
