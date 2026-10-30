# 18 --- Conventions API des composants UI

## Objectif

Rendre les composants prévisibles côté design et frontend.

## Conventions V2

-   Props nommées par intention, pas par apparence.
-   Variantes limitées et documentées.
-   Événements explicites.
-   États contrôlés/non contrôlés définis.
-   Slots/zones d'extension identifiés.
-   Aucun composant métier ne contourne les tokens.
-   Tous les composants interactifs exposent focus, disabled, loading et
    error lorsque pertinents.

## Composants primitifs

Button, IconButton, Link, Input, Select, Checkbox, Radio, Switch, Badge,
Tooltip, Popover, Dialog.

## Composants composés

DataGrid, ResourceHeader, ContextSwitcher, TaskItem, AuditEvent,
SecretField, StatusSummary, ConfigDiff.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
