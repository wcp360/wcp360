# V4.0.21.5 — cgroups v2 / systemd Enforcement Contract

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Hiérarchie canonique
`wcp-hosting.slice / reseller-or-direct-account / account / workload`.

Le nom système est généré depuis des IDs internes sûrs. Aucun nom de compte visible n’est injecté tel quel dans un unit name.

## Contrôleurs
- CPU : `cpu.weight`, `cpu.max` ;
- memory : `memory.high`, `memory.max`, swap seulement si détecté ;
- process : `pids.max` ;
- I/O : `io.weight`, `io.max` seulement si block-device attribution fiable.

## Application
`desired limits → capability check → diff → Job → closed privileged action → observed readback → compare → audit`.

Une écriture réussie sans readback ne vaut pas `VERIFIED`.

## Drift
Un reconcile détecte : mauvais cgroup, valeur divergente, controller disparu, unit absente, processus échappé. Le drift est événementisé et ne doit pas être masqué par l’UI.
