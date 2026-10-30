# 640 --- WCP Bootstrap / First Server

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Définir l'installation initiale sans dépendre d'un panel déjà
opérationnel.

## Étapes

Host preflight → package/release verification → control-plane state
initialization → root/admin bootstrap → secret/key initialization →
local node enrollment → capability discovery → service health → recovery
material/runbook confirmation.

## Sécurité

Credentials bootstrap temporaires, permissions fichiers minimales, aucun
secret dans shell history/logs lorsque évitable.
