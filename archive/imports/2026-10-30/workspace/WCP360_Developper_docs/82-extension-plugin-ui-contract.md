# 82 --- Contrat UI des extensions/plugins

## Origine

\[SOURCE\] WCP-ADMIN prévoit Marketplace, Plugin/Extension Manager, SDK
et UI Builder.

## V2

Une extension déclare : id, version, permissions, routes, navigation,
capabilities, assets, compatibilité, traductions et points d'extension.

## Sécurité

Une extension n'obtient aucune permission implicite. Les permissions
sont déclarées, approuvées et vérifiées backend.

## Design

Les extensions consomment tokens et composants WCP ; elles ne remplacent
pas le shell global.

## Défaillance

Une extension cassée ne doit pas empêcher l'accès au panneau principal.
Prévoir isolation et état `extension unavailable`.
