# MASTER RESOURCE STATE MODEL

## Modèle transversal

Chaque ressource administrable doit distinguer : - `desired_state` :
intention WCP ; - `observed_state` : état réellement observé ; -
`effective_state` : résultat après héritage/policy ; - `health` :
healthy/warning/critical/unknown selon domaine ; - `freshness` :
timestamp/source ; - `generation/version` : contrôle de concurrence
lorsque pertinent.

## Pourquoi

Ce modèle évite de présenter une configuration demandée comme déjà
appliquée et structure le reconcile après panne/offline.

## Mutations

Create/update/delete utilisent optimistic concurrency ou mécanisme
équivalent lorsque deux opérateurs peuvent modifier la même ressource.
