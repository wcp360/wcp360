# 703 --- Control Plane RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Permissions minimales

`nodes.read`, `nodes.manage`, `capabilities.read`, `jobs.read`,
`jobs.cancel`, `audit.read`, `service_accounts.read`,
`service_accounts.manage`, `control_plane.maintenance`.

## Règles

WCP-USER ne reçoit aucune permission control-plane globale. Les
permissions revendeur/client sont des scopes de ressources déléguées,
pas des permissions système.

## Step-up

Credential rotation, maintenance control-plane et opérations à fort
impact peuvent exiger MFA/step-up selon policy.
