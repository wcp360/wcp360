# V4.0.21.13 --- Canonical Cross-Registry Meta-model

## Statut

**NORMATIVE CLOSURE FRAMEWORK --- DOCUMENTATION ONLY**

Le registre canonique relie les contrats existants sans remplacer les
documents de famille.

## Ligne canonique

`module_id → resource_type → operation_id → permission_id → scope_type → entitlement_key? → quota_key? → capability_key? → lifecycle_guards → step_up_class → idempotency_class → job_contract → decision_contract → privileged_action_set → audit_event → api_contract → cli_contract → ui_action → error_set → acceptance_test_set`.

## Cardinalités

-   une Operation ID a exactement un module propriétaire ;
-   elle cible au moins un resource type ou une platform action
    explicite ;
-   elle a exactement une décision d'autorisation canonique ;
-   mutation durable : Job requis sauf exception `NO_JOB` justifiée ;
-   privileged mutation : au moins une closed Action ;
-   toute surface publique mappe vers l'Operation ID, jamais l'inverse.

## Autorité

Le registre ne peut pas inventer un champ absent du corpus. Une cellule
non fermée devient `MISSING_CONTRACT`, `DECISION_REQUIRED` ou
`NOT_APPLICABLE` avec justification.
