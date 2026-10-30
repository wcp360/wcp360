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
