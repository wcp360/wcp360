# 1070 --- Testing Strategy

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

1.  Unit : domain/policy/validators.
2.  Contract : API, IPC, adapter interfaces.
3.  Integration : PostgreSQL + real service containers/VM where valid.
4.  Security : authz, cross-tenant, traversal, injection, secret
    leakage, broker negative tests.
5.  E2E : WCP-ADMIN/WCP-USER workflows.
6.  OS certification : VM matrices Debian/Ubuntu/Alma/Rocky Tier 1.

Une feature ne devient Done sans tests d'acceptation issus de son
document 700--942 applicable.
