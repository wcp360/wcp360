# MASTER V4.0.8 --- Applications & Developer Platform

## Architecture

Website → Application → RuntimeBinding → Process/Worker/Schedule.
Application → RepositoryBinding → Deployment → ApplicationRelease.
Application → EnvironmentVariables/Secrets → DB/Cache dependencies.
WordPress/CMS Toolkits sont des couches applicatives au-dessus de ces
primitives.

## Pipeline de référence

Source/Artifact → immutable revision → staged release → dependency
install/build → migrations/hooks → release switch → process restart →
health verify → active, avec rollback lorsque techniquement valide.

## Principe de sécurité

Les scripts de build et dépendances sont du code tenant potentiellement
hostile. Ils s'exécutent sans privilège control-plane, sous limites de
ressources, filesystem scope et politiques réseau.

## Suite

La prochaine passe doit auditer **API, Automation, Billing &
Provisioning** : API REST/API-first, tokens/scopes, webhooks sortants,
idempotency, WHMCS/Blesta/FOSSBilling/HostBill, provisioning lifecycle,
suspension/unsuspension, package upgrades, external service accounts,
event delivery, rate limits et contract testing.
