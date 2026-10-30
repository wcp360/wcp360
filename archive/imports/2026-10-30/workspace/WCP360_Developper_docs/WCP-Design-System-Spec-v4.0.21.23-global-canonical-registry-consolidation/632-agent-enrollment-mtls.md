# 632 --- Agent Enrollment & mTLS

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Workflow proposé

Bootstrap token à durée limitée → demande d'enrollment → identité nœud →
certificat client → validation control plane → activation → rotation
automatique.

## Sécurité

mTLS pour communications agent↔control plane lorsque distantes ;
CA/trust roots gouvernés ; certificats courts/rotatifs selon politique ;
révocation et re-enrollment.

## Interdit

Token bootstrap permanent, secret d'enrollment dans logs, acceptation
silencieuse d'un changement d'identité nœud.
