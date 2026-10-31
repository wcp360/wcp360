# MASTER OPEN DECISIONS --- V3.6

## Décisions fermées en Phase 1

Audit retention, navigateurs, OS baseline, package strategy,
control-plane DB, jobs/events V1, SecretStore V1, telemetry retention,
release/agent compatibility, control-plane DR targets, backup baseline,
mail baseline, DNS baseline et web/PHP baseline sont désormais couverts
par ADR-001 à ADR-014.

## Décisions encore ouvertes

1.  HA multi-control-plane : topology/quorum/leader election du profil
    HA.
2.  Cluster distributed storage : Ceph/ZFS roles et fencing certifiés
    par topology.
3.  Compute/SDN : scope V1 exact de KVM/LXC/virtual networking.
4.  AI : modèles/providers, data residency et activation par édition.
5.  Licensing : fournisseur/serveur de licence et offline grace policy.
6.  Enterprise external KMS/HSM : providers certifiés.
7.  Architectures CPU supplémentaires : arm64 certification.
8.  Kubernetes/bare-metal/IaC : roadmap P2.

Aucune décision ouverte ci-dessus ne bloque le **profil V1 Standalone
Hosting**.
