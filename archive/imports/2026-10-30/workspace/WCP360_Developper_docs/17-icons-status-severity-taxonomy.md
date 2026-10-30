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
