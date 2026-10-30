# V4.0.19 — wcpd RPC Authorization & Target Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Global deny
Unknown method, unknown protocol, missing Job correlation, invalid peer credential, target outside allowlist, path traversal/symlink escape or expired deadline = DENY. No end-user password is an authorization proof.

| RPC | Internal authorization | Allowed targets | Adapter | Required evidence |
|---|---|---|---|---|
| `UserCreate/UserLock/UserUnlock/UserDelete` | account.* internal job | canonical account UID/GID only | system identity | re-observe passwd/account state |
| `GroupEnsure` | account/site internal job | WCP-managed groups only | system identity | group membership |
| `OwnershipApply/PermissionReconcile` | filesystem jobs | /srv/wcp-data managed roots + explicit WCP config roots | filesystem | lstat/ownership/mode without symlink escape |
| `QuotaApply` | quota.apply/account.provision | certified WCP data filesystem only | filesystem/quota | quota observed |
| `DirectoryEnsure` | provision/restore | allowlisted managed roots | filesystem | directory owner/mode |
| `ManagedFileInstall` | config generation/deploy | WCP-owned generated paths only | managed config | digest/path/owner |
| `ManagedGenerationActivate` | config jobs | service-specific WCP generation roots | managed config | active generation fingerprint |
| `ServiceReload/Restart/Enable/Disable/Status` | service jobs | allowlisted service units only | systemd | unit state + service verify |
| `NftablesValidateApply` | firewall job | WCP-owned nftables table/chains | nftables | ruleset/listener/admin-path check |
| `ListenerInspect` | readiness/security | local listeners only | network observation | listener evidence |

## Fermeture
Chaque RPC V1 possède caller class, target boundary et evidence. **RPC METHOD AUTHORIZATION GAP = CLOSED.**
