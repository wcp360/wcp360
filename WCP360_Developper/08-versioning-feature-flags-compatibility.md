# 08 --- Versioning, capacités et feature flags

## Objectif

Permettre à WCP de fonctionner avec des serveurs/nœuds de capacités
différentes.

## Capabilities V2

L'API expose les capacités réellement disponibles : services installés,
versions, modules, licence, permissions, flags expérimentaux.

## UI

Une fonction indisponible est : - masquée si elle n'a aucun sens dans le
contexte ; - affichée désactivée avec explication si sa découverte est
utile ; - marquée `Beta` ou `Experimental` si nécessaire.

## Compatibilité

Les écrans ne supposent pas qu'Apache, Nginx, Postfix, MariaDB,
PostgreSQL, Docker ou KVM sont toujours présents. Le module s'adapte aux
capacités déclarées.

## Dépréciation

`Stable → Deprecated → Removed`. Toute dépréciation documente
remplacement, date cible et impact.
