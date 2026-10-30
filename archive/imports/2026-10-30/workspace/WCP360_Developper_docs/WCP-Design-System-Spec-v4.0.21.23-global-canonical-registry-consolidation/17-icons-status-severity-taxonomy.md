# 17 --- Taxonomie des états, santé et gravité

## Axes distincts

### Status

Active, Suspended, Provisioning, Updating, Deleting, Disabled.

### Health

Healthy, Degraded, Critical, Unknown, Maintenance.

### Severity

Info, Success, Warning, Error, Critical.

### Job state

Queued, Waiting approval, Running, Succeeded, Succeeded with warnings,
Failed, Cancelled, Timed out.

## Règle

Ne jamais réutiliser un même badge pour des concepts différents. Icône +
texte + couleur sémantique.

## Unknown

`Unknown` n'est jamais affiché comme `Healthy`. L'absence de télémétrie
est un état explicite.

## V4.0.10 normative deepening

- `OPS-4010-18-STATUS-COMPONENTS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
