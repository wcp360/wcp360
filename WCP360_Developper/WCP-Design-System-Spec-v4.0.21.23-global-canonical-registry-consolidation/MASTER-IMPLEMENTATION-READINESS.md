# MASTER IMPLEMENTATION READINESS --- V3.6 PHASE 1

## Légende

`READY` = contrat, dépendances et décisions bloquantes V1 identifiés.
`PARTIAL` = contrat présent mais décisions/test matrix restent.
`OPTIONAL/FUTURE` = hors cœur V1.

  -----------------------------------------------------------------------------
  Domaine                     Statut                  Fermeture V3.6
  --------------------------- ----------------------- -------------------------
  Design / UX / WCP-USER /    READY                   déjà structuré
  WCP-ADMIN                                           

  Secure Development          READY                   norme 650--669

  API-first / Service         READY                   contrat API + ADR release
  Accounts / CLI                                      

  System / OS                 READY                   ADR-001/002

  Web / PHP                   READY                   ADR-014 + package adapter

  DB                          READY                   OS/package baseline +
                                                      backup/upgrade gates

  Mail                        READY                   ADR-012

  DNS                         READY                   ADR-013

  Backup                      READY                   ADR-011

  Network / SSH / Security    READY                   anti-lockout + norme
                                                      sécurité + OS baseline

  Telemetry                   READY                   ADR-008

  WCP Control Plane V1        READY                   ADR-003/004/005/006/010
  Standalone                                          

  Secret Store V1             READY                   ADR-005

  Audit retention/export      READY                   ADR-007

  Supported browsers          READY                   ADR-009

  Certified OS baseline       READY                   ADR-001

  Package/repository strategy READY                   ADR-002

  Cluster / distributed       PARTIAL                 HA/fencing/production
  storage                                             certification encore à
                                                      tester

  Compute / SDN               PARTIAL                 dépend du cluster/network
                                                      test matrix

  AI execution                PARTIAL                 approval/evidence
                                                      existent,
                                                      modèles/providers non
                                                      figés

  Licensing commercial        PARTIAL                 règles business/provider
                                                      non figées

  Bare-metal/Kubernetes/IaC   OPTIONAL/FUTURE         P2
  -----------------------------------------------------------------------------

## READY ne signifie pas « codé »

Il signifie que l'équipe peut désormais produire schémas API,
migrations, adapters et tests sans décision architecturale bloquante
pour le profil V1 concerné.
