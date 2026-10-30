# V4.0.21.12 --- Cross-registry Integrity Rules

## Registries à croiser

Resource, Operation, Permission, Scope, Entitlement, Quota, Capability,
Job, Decision, Privileged Action, Audit Event, Error Code, API Route,
CLI Command, UI Action, Adapter Capability, Acceptance Test.

## Contraintes

-   toute Operation référence des IDs existants;
-   aucune Permission orpheline sans opération/usage documenté;
-   aucun Job sans opération déclenchante ou tâche système nommée;
-   aucune privileged Action sans caller Job autorisé;
-   aucun entitlement sans owner/module/value type;
-   aucun module visible sans capability/readiness model;
-   aucun backend-specific option dans le contrat canonique sans
    capability gate.
