# V4.0.21.12 --- API / CLI / UI Parity Gate

## Règle

Toute action WCP-ADMIN/WCP-USER doit converger vers le même Operation ID
et le même policy engine. Les surfaces peuvent masquer des actions
indisponibles, mais ne peuvent pas modifier le résultat d'autorisation.

## Exceptions documentées

Installer bootstrap, emergency break-glass et internal reconciliation
peuvent avoir une surface dédiée, mais leurs privilèges, audit et durée
sont explicites.

## Gate

Une opération mutationnelle sans route/API contract ou sans mapping CLI
lorsque la CLI est promise reste `PARTIAL`.
