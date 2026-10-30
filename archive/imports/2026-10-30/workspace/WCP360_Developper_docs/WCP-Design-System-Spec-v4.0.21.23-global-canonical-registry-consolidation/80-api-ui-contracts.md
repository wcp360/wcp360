# 80 --- Contrats API ↔ UI

## Objectif V2

Éviter que l'UI déduise des permissions ou états à partir de conventions
fragiles.

## Envelope logique recommandé

Ressource + capabilities + metadata. Les erreurs possèdent un code
stable, message utilisateur localisable côté UI si stratégie retenue,
détails techniques séparés et correlation/request id.

## Mutation

Retourne résultat immédiat ou référence de job. L'UI ne simule pas une
réussite avant confirmation.

## Pagination

Le choix offset/cursor relève de l'API ; le Data Grid doit supporter le
modèle retenu sans supposer le nombre total disponible.

## Permissions

Les capacités actionnables peuvent être renvoyées avec la ressource ; le
backend reste autorité finale.

## Concurrence

Version/ETag ou mécanisme équivalent lorsque les modifications
concurrentes présentent un risque.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
