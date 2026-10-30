# V4.0.21.12 --- Lifecycle & Deletion Consistency Audit Contract

## États transversaux

Création/provisioning, ready/active, suspended/disabled, degraded/error,
deleting, deleted/archived doivent avoir une sémantique propre à la
ressource.

## Règles

Suspend ≠ delete. Archive ≠ purge. Desired delete ≠ immediate
irreversible destruction. Parent deletion requires child disposition
graph. Retention/legal hold precedes purge. Restore cannot resurrect
ownership inconsistently.

## Downgrade

Un changement plan/quota ne détruit jamais silencieusement les données.
`NONCOMPLIANT` + remediation est le modèle par défaut.
