# V4.0.21 --- Resource-specific Logical Schema Registry

## Statut

**NORMATIVE --- V4.0.21 DETERMINISTIC CONTRACT CLOSURE --- NO CODE**

## Mandat

Cette passe ferme exclusivement les 12 blockers P0 de
`AUDIT-V4020-02-P0-BLOCKERS.md`. Aucune capability produit n'est
ajoutée. Les identifiants ci-dessous sont des contrats de conception non
exécutables.

## Règle

Aucun `CRUD`, wildcard `*`, `when needed`, `policy based`,
`according to adapter`, `if large` ou `N/A` non justifié n'est autorisé
dans un registry normatif final.

## Conventions

IDs are opaque immutable identifiers. `version` is
optimistic-concurrency version. Timestamps are UTC. `*_ref` references a
typed resource/secret, never raw secret material.

  ------------------------------------------------------------------------------------------------------------------------------------------------------------
  Resource               Required logical fields      Uniqueness / invariants                         Relations                      Delete semantics
  ---------------------- ---------------------------- ----------------------------------------------- ------------------------------ -------------------------
  `tenant`               id, slug, display_name,      slug global                                     parent none                    RESTRICT while
                         status, version                                                                                             accounts/users/bindings
                                                                                                                                     exist

  `user`                 id, login, email,            normalized login global; normalized email       bindings/sessions/tokens       disable first; purge only
                         display_name, auth_state,    according identity policy                                                      after retention and no
                         version                                                                                                     legal hold

  `role_binding`         id, actor_id, role_id,       actor+role+scope unique                         actor/role/scope               hard delete allowed;
                         scope_type, scope_id,                                                                                       audit retained
                         delegated_by, version                                                                                       

  `reseller`             id, tenant_id, account_id,   one reseller profile per hosting account        tenant/account/tree            RESTRICT while
                         parent_reseller_id, status,                                                                                 descendants exist
                         version                                                                                                     

  `plan`                 id, tenant_id, code,         tenant+code unique                              entitlements/accounts          RESTRICT while assigned;
                         display_name, status,                                                                                       archive allowed
                         version                                                                                                     

  `entitlement`          id, plan_id, capability_key, plan+capability_key unique                      plan                           replace/delete only
                         limit_kind, limit_value,                                                                                    through plan mutation
                         version                                                                                                     

  `hosting_account`      id, tenant_id, reseller_id?, tenant+system_name; uid global; gid global      sites/db/mail/cron/data-root   orchestrated delete after
                         plan_id, system_name, uid,                                                                                  dependency policy
                         gid, status, version                                                                                        

  `domain`               id, tenant_id, account_id,   fqdn_ascii globally unique among managed        account/site/zone/mail         detach dependents then
                         fqdn_ascii, fqdn_unicode,    domains                                                                        observed absence
                         ownership_mode, site_id?,                                                                                   
                         dns_zone_id?, mail_enabled,                                                                                 
                         tls_policy, status, version                                                                                 

  `site`                 id, tenant_id, account_id,   account+document_root_rel unique                account/domain/runtime         orchestrated delete; data
                         primary_domain_id?,                                                                                         retention explicit
                         document_root_rel,                                                                                          
                         runtime_profile_id, status,                                                                                 
                         version                                                                                                     

  `runtime_profile`      id, site_id, runtime_kind,   one active profile per site                     site                           replace by versioned
                         runtime_version,                                                                                            update; no independent
                         entrypoint?,                                                                                                purge
                         env_secret_set_ref?, version                                                                                

  `certificate`          id, tenant_id,               active certificate uniqueness per subject+usage domain/secret refs             revoke/expire; metadata
                         subject_domain_id, provider,                                                                                retained
                         key_ref, chain_ref,                                                                                         
                         not_before, not_after,                                                                                      
                         state, version                                                                                              

  `dns_zone`             id, tenant_id, fqdn_ascii,   fqdn_ascii unique for managed-local zone        records/domain                 RESTRICT or orchestrated
                         mode, provider_ref?, serial,                                                                                detach
                         dnssec_state, status,                                                                                       
                         version                                                                                                     

  `dns_record`           id, zone_id, owner_rel,      zone+owner+type+rdata unique; CNAME exclusivity zone                           hard delete after
                         rrtype, rdata_canonical,     invariant                                                                      provider verify
                         ttl, version                                                                                                

  `database`             id, tenant_id, account_id,   engine+system_name unique                       users/backups                  orchestrated delete after
                         engine, logical_name,                                                                                       dependency/retention
                         system_name, charset,                                                                                       
                         status, version                                                                                             

  `database_user`        id, database_id,             engine+system_name unique                       database/secret                revoke grants then delete
                         system_name, secret_ref,                                                                                    principal
                         grants_profile, status,                                                                                     
                         version                                                                                                     

  `mail_domain`          id, tenant_id, account_id,   domain_id unique                                mailboxes/aliases              RESTRICT while children
                         domain_id, status,                                                                                          unless cascade explicitly
                         policy_id?, version                                                                                         requested

  `mailbox`              id, mail_domain_id,          mail_domain+localpart_norm unique               domain/secret                  suspend → retention →
                         localpart_norm,                                                                                             purge
                         address_ascii, secret_ref,                                                                                  
                         quota_bytes, auth_state,                                                                                    
                         delivery_state, version                                                                                     

  `mail_alias`           id, mail_domain_id,          mail_domain+source_localpart_norm+destination   mail_domain                    hard delete after routing
                         source_localpart_norm,       unique                                                                         verify
                         destination_kind,                                                                                           
                         destination_value, status,                                                                                  
                         version                                                                                                     

  `mail_policy`          id, scope_type, scope_id,    scope_type+scope_id unique                      mail domain/account/tenant     replace/version; retain
                         spam_threshold,                                                                                             audit
                         quarantine_mode,                                                                                            
                         outbound_limit_profile,                                                                                     
                         version                                                                                                     

  `backup_policy`        id, tenant_id, scope_type,   one active policy per scope+purpose             integration/scope              archive; existing runs
                         scope_id, schedule_spec,                                                                                    immutable
                         retention_spec, provider_id,                                                                                
                         encryption_policy, status,                                                                                  
                         version                                                                                                     

  `backup_run`           id, policy_id?, scope_type,  id unique; terminal run immutable               policy/scope/manifest          immutable; retention
                         scope_id, manifest_ref,                                                                                     purge only
                         started_at, completed_at?,                                                                                  
                         state,                                                                                                      
                         immutable_policy_snapshot,                                                                                  
                         version                                                                                                     

  `restore_run`          id, backup_run_id,           id unique                                       backup/target                  immutable execution
                         target_scope,                                                                                               record
                         conflict_policy, started_at,                                                                                
                         completed_at?, state,                                                                                       
                         version                                                                                                     

  `cron_job`             id, account_id, schedule,    account+canonical schedule+command identity     account                        delete then reconcile
                         command_profile,                                                                                            absence
                         working_dir_rel, enabled,                                                                                   
                         version                                                                                                     

  `api_token`            id, user_id, token_hash,     token fingerprint unique                        user                           revoke; secret material
                         scopes, expires_at,                                                                                         never stored retrievable
                         revoked_at?, version                                                                                        

  `secret_ref`           id, tenant_id?, purpose,     id unique                                       owning resources               metadata retained;
                         active_version, state,                                                                                      material versions
                         created_at, rotated_at?,                                                                                    separately
                         version                                                                                                     lifecycle-managed

  `integration`          id, tenant_id?, kind,        scope+kind+name unique                          secret/provider                disable before delete;
                         endpoint_ref,                                                                                               preserve audit
                         credential_secret_ref?,                                                                                     
                         config_safe, state, version                                                                                 

  `job`                  id, type, resource_type,     idempotency tuple unique while retained         resource/attempts/events       immutable terminal
                         resource_id,                                                                                                outcome; purge by ops
                         desired_version, state,                                                                                     retention
                         retry_class, lease_owner?,                                                                                  
                         attempt, idempotency_key?,                                                                                  
                         version                                                                                                     

  `event`                id, type, resource_type,     id unique                                       resource/job                   append-only retention
                         resource_id,                                                                                                
                         resource_version,                                                                                           
                         occurred_at, correlation_id,                                                                                
                         payload_safe                                                                                                

  `audit_record`         id, action_id,               append-only id                                  actor/resource/job             no user delete; policy
                         real_actor_id,                                                                                              retention only
                         effective_actor_id?, scope,                                                                                 
                         resource_type, resource_id,                                                                                 
                         outcome, correlation_id,                                                                                    
                         job_id?, safe_diff,                                                                                         
                         occurred_at                                                                                                 

  `notification_route`   id, tenant_id?, kind,        scope+kind+destination unique                   secret                         disable/delete; delivery
                         destination_ref,                                                                                            audit retained
                         secret_ref?, event_filter,                                                                                  
                         enabled, version                                                                                            

  `security_finding`     id, tenant_id?,              active fingerprint unique                       resource                       resolve/suppress; retain
                         finding_type, resource_type,                                                                                history
                         resource_id, severity,                                                                                      
                         state, evidence_ref,                                                                                        
                         first_seen, last_seen,                                                                                      
                         version                                                                                                     

  `node`                 id, stable_machine_id_hash,  single V1 active node identity                  services/jobs                  cannot delete active V1
                         hostname, role,                                                                                             node
                         lifecycle_state,                                                                                            
                         observed_health, last_seen,                                                                                 
                         version                                                                                                     
  ------------------------------------------------------------------------------------------------------------------------------------------------------------

## Cross-field invariants

-   A managed `domain.fqdn_ascii` is canonical lowercase IDNA A-label
    and cannot belong to two active tenants.
-   `dns_record` CNAME owner cannot coexist with other data records at
    the same owner except DNSSEC/system records explicitly permitted by
    the DNS engine contract.
-   `mailbox.address_ascii = localpart_norm + @ + mail_domain.domain.fqdn_ascii`.
-   `site.account_id`, bound domain account, runtime site and document
    root owner must resolve to the same hosting account.
-   A terminal `backup_run`, `restore_run`, `event` and `audit_record`
    is immutable except retention metadata.
-   `secret_ref` never contains plaintext material.
