# CURRENT BASELINE NOTICE — V4.0.21.23

**NO CODE — CODE BLOCKED.** Le corpus reste en phase de spécification et de fermeture canonique globale.

Le titre historique `Spécification maître v3.3` ci-dessous est conservé comme historique ; il ne représente pas la version active.

# WCP Design System --- Spécification maître v3.3

## Statut

**Base de référence pour conception, prototypage, développement et QA.**

WCP --- Web Control Panel est composé de deux expériences :

-   **WCP-USER** : administration de l'hébergement par client et
    revendeur.
-   **WCP-ADMIN** : administration serveur, infrastructure, services et
    orchestration.

## Origine des exigences

Deux catégories sont distinguées dans toute la V2 :

1.  **\[SOURCE\]** : fonction issue des documents fonctionnels WCP-USER
    / WCP-ADMIN fournis au projet.
2.  **\[V2\]** : règle d'industrialisation, UX, sécurité, architecture
    UI ou qualité proposée pour rendre le produit cohérent et
    développable à grande échelle.

Une proposition V2 ne remplace pas silencieusement une exigence métier
source.

## Principes non négociables

-   Simple par défaut, puissant à la demande.
-   Une seule grammaire UI ; deux expériences adaptées aux
    responsabilités.
-   RBAC + scopes + capacités vérifiés côté serveur.
-   Toute mutation sensible est auditable.
-   Toute tâche longue est asynchrone et observable.
-   Toute destruction annonce son impact avant exécution.
-   Aucun secret n'est exposé inutilement.
-   WCAG 2.2 AA, clavier, zoom 200 %, i18n, RTL.
-   Light / Dark / System par tokens sémantiques.
-   L'IA suit : **Observer → Analyser → Proposer → Faire approuver →
    Exécuter → Vérifier → Auditer**.
-   L'interface reste utilisable si l'IA est désactivée.

## Architecture documentaire

-   `00–09` : produit, architecture, gouvernance, sécurité.
-   `10–19` : foundations.
-   `20–29` : composants.
-   `30–39` : patterns transversaux.
-   `40–49` : WCP-USER.
-   `50–59` : WCP-ADMIN.
-   `60–69` : IA, automatisation, observabilité.
-   `70–79` : inventaires, workflows, livraison.
-   `80–89` : contrats techniques front/back et extension.

## Convention de maturité

Chaque page et composant porte un statut : `Draft`, `Ready for design`,
`Ready for dev`, `Beta`, `Stable`, `Deprecated`.

## Gate de livraison

Une fonctionnalité P0 n'est pas livrable si l'un de ces éléments manque
:   permissions backend, états loading/empty/error/denied, audit des
    mutations sensibles, responsive, accessibilité, i18n, gestion de
    concurrence, feedback d'opération, tests du workflow principal.

## Extension V3 --- Modules techniques

La V3 ajoute une couche métier WCP-ADMIN détaillée dans
`99-module-catalog.md` et les séries `100–290`. Elle ne remplace pas les
foundations et patterns : elle les applique aux services système réels.

## Extension V3.1

Ajout des modules de sécurité/conformité, isolation/quota, runtimes
applicatifs, Git/CI-CD, dépendances, LiteSpeed/OpenLiteSpeed,
Redis/Memcached, SSO DB, workers/queues, billing/provisioning et File
Manager complet. Voir `98-v31-security-app-platform-additions.md`.

## Extension V3.2 --- Hosting Platform Experience

Ajout du WordPress Toolkit, catalogue d'applications,
multi-tenant/Plans/Impersonation, contrat API-first, connecteurs billing
étendus, automation événementielle, webmail/autoconfig/Sieve et
white-label. Voir `97-v32-gap-review.md` et la série `360–378`.

## Extension V3.3 --- Completeness Pass

Ajout des domaines/registrars, Migration Center, mail avancé, DB
import/performance, vérification backup, IPAM/metering, host security,
storage hardware, Enterprise SSO, support et licences. Voir
`96-v33-completeness-pass.md`, `MASTER-FEATURE-MATRIX.md` et
`MASTER-COMPLETENESS-CHECKLIST.md`.

## Extension v3.5 --- Systems & Operations Completeness

Ajout des couches Linux network, DNS authoritative, mail deliverability,
telemetry, backup adapters, shared storage, SSH, MFA recovery/SCIM,
Service Accounts/CLI/IaC, bare-metal/Kubernetes extension points, plugin
trust, SIEM et ACME/TLS avancé. Voir
`95-v34-systems-operations-completeness.md` et les trois matrices
MASTER.

## V3.5 --- Architecture Normalization & Implementation Readiness

Cette passe n'ajoute pas de fonctions d'hébergement. Elle normalise les
sources de vérité, classe les chevauchements, remplace le rapport de
cohérence obsolète et spécifie le fonctionnement du control plane WCP
(`630–641`). Les points d'entrée sont `MASTER-CANONICAL-SOURCE-MAP.md`,
`MASTER-IMPLEMENTATION-READINESS.md` et `MASTER-OPEN-DECISIONS.md`.

## V3.5 --- Deep normalization pass

La deuxième passe V3.5 ajoute les contrats transversaux nécessaires pour
rendre les modules implémentables sans inventer les technologies encore
non décidées. Consulter `V35-DEEP-NORMALIZATION-REPORT.md` et
`MASTER-NORMALIZATION-ROADMAP.md`.

## Norme de sécurité de développement V3.5

`MASTER-SECURE-DEVELOPMENT-STANDARD.md` et `650–669` sont désormais les
documents normatifs pour langages, stockage Linux, naming, permissions,
PHP/Go, broker root, chemins, secrets, systemd, build et déploiement.

## V3.6 Readiness Phase 1

Audit systématique par familles, registre des documents, graphe de
dépendances et ADR-001 à ADR-014 ferment les décisions bloquantes du
profil V1 Standalone Hosting.

## V3.7 --- Implementation Contracts Phase 1

Les familles Control Plane, System/OS, API Platform, Network et Web/PHP
disposent désormais de contrats d'implémentation `700–745`, sans
contourner les normes de sécurité `650–669`.

## V3.7 --- Implementation Contracts Phase 2

DNS/TLS, Mail, Database/Cache, Backup/Storage et Security/SSH passent au
niveau `IMPLEMENTATION-READY` via `750–795`.

## V3.7 --- Implementation Contracts Phase 3

Les familles orientées hébergeur/client `800–874` sont désormais
contractualisées : Domains/Registrar, Accounts/Plans/Reseller, File
Manager, Apps/CMS, Developer/Git/Runtimes, Migration,
Billing/Provisioning et Monitoring/Telemetry.

## V3.8 --- Closure Audit Phase 4

La passe V3.8 ferme Identity/SSO, Notifications, Scheduler/Workers,
Audit/Logs et Support/KB au niveau implementation-ready, tout en
conservant explicitement Licensing commercial, Marketplace tiers,
HA/Cluster, Compute/Kubernetes avancé et IA hors du chemin critique V1
tant que leurs décisions ne sont pas fermées.

## V3.9 --- Developer Blueprint

Le corpus contient désormais une traduction du V1 implementation-ready
vers repository topology, PHP/Go boundaries, PostgreSQL, OpenAPI, RBAC,
events/jobs, IPC, adapter SDK, tests/CI, packaging et roadmap Sprint 0 →
MVP → V1.

## V4.0 --- Implementation Foundation

Le Sprint 0 est maintenant détaillé jusqu'aux DDL logiques initiaux,
Permission Registry, erreurs/OpenAPI, IPC v1, Action Registry, arbres
PHP/Go, systemd, CI, matrice Tier 1 et flow install/upgrade.

## V4.0.1 — Deep Completeness Audit
Phase NO-CODE : audit exhaustif du corpus V4.0 avant tout passage à l'implémentation.

## V4.0.2 — Foundations Deep Specification
Passe A approfondie. NO-CODE gate maintenu ; prochaine passe : System / OS.

## V4.0.3 — System & OS Deep Specification
Passe B approfondie, 30 contrats normatifs System/OS. NO-CODE gate maintenu.

## V4.0.4 — Hosting Core Deep Specification — Tranche 1
Accounts/Plans/Reseller, Domains, Web engines et PHP approfondis. NO-CODE gate maintenu.

## V4.0.5 — Hosting Core Deep Specification — Tranche 2
DNS/TLS/ACME, databases, Redis/Memcached et DB tools/SSO approfondis. NO-CODE maintenu.

## V4.0.6 — Mail & Deliverability Deep Specification
Postfix/Dovecot, mail resources, authentication, deliverability, anti-spam/AV, queue, reputation, webmail, backup/migration et security approfondis. NO-CODE maintenu.

## V4.0.7 — Files, Backup, Security & Recovery Deep Specification
File Manager, SFTP/SSH, backup/restore/DR, malware, intrusion prevention et Security Center approfondis. NO-CODE maintenu.

## V4.0.8 — Applications & Developer Platform Deep Specification
CMS/WordPress, runtimes, dependencies, Git/CI-CD, processes/workers/cron, releases, secrets et supply-chain approfondis. NO-CODE maintenu.

## V4.0.9 — API, Automation, Billing & Provisioning Deep Specification
API-first, tokens/scopes, idempotency, webhooks, billing adapters et provisioning/reconciliation approfondis. NO-CODE maintenu.

## V4.0.10 — Observability, Operations, Notifications & Incident Management
Health, metrics, logs, traces, alerting, SLO, incidents, maintenance, notifications, diagnostics et capacity approfondis. NO-CODE maintenu.

## V4.0.11 — Multi-node, Cluster, HA & Distributed Control Plane
Enrollment/trust, placement, leases/locks, quorum, failover, replication boundaries, HA, rolling upgrades et cluster DR approfondis. NO-CODE maintenu.

## V4.0.12 — Lifecycle, Updates, Compatibility & Platform Governance
Releases, provenance, channels, upgrades, OS/packages, migrations, feature flags, compatibility/EOL, rollback/recovery, entitlements/licensing et update governance approfondis. NO-CODE maintenu.

## V4.0.13 — Final Cross-Corpus Completeness & Contradiction Audit
Le corpus est désormais gelé comme baseline de spécification. Les marqueurs bruts ont été classifiés, les références/duplications contrôlées et les ADR restants isolés. NO-CODE reste actif jusqu'à fermeture des ADR et du scope V1.

## V4.0.14 — Installation, Bootstrap, First-run, Finalization, Repair & Server Lifecycle
Dernière famille fonctionnelle issue de l'audit : installer/preflight/wizard, mail security, readiness, repair/rescue, transfer, reconfiguration, emergency lifecycle et decommission. NO-CODE maintenu.

## V4.0.15 — Final Completeness Re-Audit & Scope Classification
Functional specification freeze. No new mandatory architecture family identified. V1 scope classified and ADR closure register established. NO-CODE remains active.

## V4.0.16 — ADR Closure & V1 Architecture Freeze
Les 20 ADR P0 sont fermés. Architecture V1 gelée. Aucun code ni artefact exécutable n'est créé. La prochaine couche autorisée est la spécification d'implémentation non exécutable.

## V4.0.17 — Canonical Implementation Contracts
Traduction non exécutable de l’architecture V1 gelée : resource/data model, DB logique, API, Jobs, wcpd RPC, filesystem/systemd/config generation, security/E2E tests et versioning. NO CODE.


## V4.0.18 — Implementation Readiness & Traceability Audit
Audit strict de V4.0.17. Architecture PASS, traceability PARTIAL, Repository Entry Gate FAIL. Aucun code. V4.0.19 doit fermer les registries et mappings P0.


## V4.0.19 — Traceability Closure
Fermeture des 7 findings P0 et des 12 livrables du backlog V4.0.18. Toutes les lignes CORE V1 sont PASS ou N/A justifié. Aucun code. Repository Entry reste pending jusqu'à un audit final indépendant V4.0.20.

## V4.0.20 — Independent Repository Entry Audit
L'audit indépendant réfute plusieurs faux PASS de granularité V4.0.19. 12 blockers P0 restent à fermer. Repository skeleton et code restent interdits. Prochaine passe : V4.0.21 Deterministic Contract Closure.

## V4.0.21 — Deterministic Contract Closure
Les 12 P0 de V4.0.20 sont fermés par des contrats resource/action/job/RPC/RBAC/audit/test déterministes. NO CODE. Repository Entry reste pending V4.0.22 independent re-audit.

## V4.0.21.1 — Corrective Errata
Le verdict 12/12 P0 PASS de V4.0.21 est retiré. Plusieurs P0 restent PARTIAL. Repository et code restent bloqués; V4.0.22 attend une fermeture complémentaire.

## V4.0.21.1.1 — Plan / Entitlement / Demo / Trial
Ajout normatif de la gestion des packages, feature lists, quotas, overrides par compte, ceilings revendeur, modes STANDARD/DEMO/TRIAL/SUSPENDED et simulation DEMO isolée. Ces contrats doivent être intégrés aux registries exhaustifs V4.0.21.2 avant tout Repository Entry.

## V4.0.21.2 — Deterministic Integration
Plan/Entitlement/Demo/Trial is now bound to logical schemas, exact API IDs, Jobs/decisions/adapters, acceptance and retention. Global corpus closure is not yet certified; repository and code remain blocked.

## V4.0.21.3 — Marker Triage
Les 262 documents contenant `PARTIAL` ou `DECISION REQUIRED` ont été classifiés en REAL_OPEN_GAP/HISTORICAL/SUPERSEDED/FALSE_POSITIVE. Aucun gap n'est déclaré fermé par simple reclassification. Repository et code restent bloqués.

## V4.0.21.3.1 — Corrected Gap Triage
Manual cross-check against accepted ADRs corrects the heuristic marker triage. Current repository blockers are B1–B5 deterministic coverage, while commercial licensing and roadmap/Enterprise decisions remain open but non-blocking for V1 Standalone.

## V4.0.21.4 — Canonical V1 Universe
Canonical V1 Standalone module/resource/action universe added. This is the scope freeze for B1/B2 closure; incomplete rows remain explicitly blocked.

## V4.0.21.4.1 — Commands / Options / Isolation
Canonical CLI surface, command families, layered isolation, backend options, resource limits, filesystem/process boundaries, isolation operations and acceptance threats. Linux native cgroups v2/systemd/UID-GID is the V1 default; commercial/advanced isolation is adapter-based.

## V4.0.21.5 — Isolation & Command Contracts
Closes the isolation-family B1/B2 contract set: resource dictionaries, effective composition, cgroups/systemd, filesystem/quota, runtime/IPC/network, exact API operations, jobs/privileged actions, CLI safety and P0 acceptance. Global repository/code gates remain blocked.

## V4.0.21.6 — Identity / Auth / RBAC
Adds canonical B1 dictionaries, exact B2 operation registries, policy evaluation, MFA/session/recovery, hierarchy, CLI/API/UI parity and P0 security acceptance for the Identity/Auth/RBAC family.

## V4.0.21.7 — Account / Reseller / Plan / Quota
Adds B1/B2 contracts for hosting accounts, system identities, reseller ceilings, plans/entitlements, quotas/isolation assignment, lifecycle, execution bindings, retention and acceptance.

## V4.0.21.8 — Web Stack
Adds B1/B2 contracts for domains/sites, Nginx/Apache, PHP/PHP-FPM, TLS/ACME and DNS/DNSSEC, including options/capability detection, CLI mapping, atomic apply/rollback and P0 acceptance.

## V4.0.21.9 — Database / Cache / Mail Security
Adds B1/B2 contracts for databases, Redis/Memcached, mail domains/mailboxes, anti-spam/AV/Sieve, deliverability records, webmail SSO, queue/failure handling, CLI mapping and P0 acceptance.

## V4.0.21.10 — Files / Access / Apps / Runtimes Deep Closure
Deep reconciliation and closure of File Manager, SFTP/FTP/SSH, Git/deploy, app installer, WordPress Toolkit, runtimes, dependency managers, secrets, cron/workers and webhook security.

## V4.0.21.11 — Operations / Security / Installer Deep Closure
Deep closure for Backup/Restore/DR, malware scanning, firewall/WAF/anti-bruteforce, monitoring/logs/alerts, updates, Doctor and transactional installer/finalization/readiness.

## V4.0.21.12 — Global Closure Audit
Adds a factual marker scan, global dependency graph, B1/B2 closure gates, cross-registry integrity rules, lifecycle/security invariants, module completeness matrix, DEMO/plan privilege closure and global READY criteria.

## V4.0.21.13 — Canonical Cross-Registry
Defines the canonical resource/operation/permission/scope/entitlement/job/decision/action/surface registries, source-of-truth and supersession rules, dependency edges, missing-contract taxonomy, and a factual marker triage/review queue.

## V4.0.21.14 — Competitive Gap Closure
Adds IPAM/network, CloudLinux/MySQL Governor integration abstractions, APM, containers, advanced mail operations, web compatibility utilities, WordPress Safe Updates, migration/import and multi-node primitives.

## V4.0.21.15 — Final Completeness & PARTIAL Closure
Reconciles historical vs active markers, establishes normative authority, closes baseline product decisions, defines host-profile certification and global competitive/NFR/P0 readiness gates.

## V4.0.21.16 — Core Security Registry Population
Populates the canonical registry for identity/auth/MFA/session, RBAC/scope/impersonation, reseller/account/plan, entitlement/quota/DEMO, isolation/system identity/filesystem and Job/Decision/Privileged Action.

## V4.0.21.17 — Web/PHP/TLS/DNS Registry Population
Populates canonical contracts for domain/site, webserver/vhost, PHP-FPM, TLS/ACME and DNS/DNSSEC.

## V4.0.21.18 — Database / Cache Registry Population
Populates database principals/grants, MySQL/MariaDB/PostgreSQL adapters, Redis/Memcached, DB governor, backup/import/export and secure admin-tool SSO.

## V4.0.21.19 — Mail / Security / Deliverability Registry
Populates canonical mail domain/mailbox, SMTP/IMAP, SPF/DKIM/DMARC, anti-spam/AV, Sieve, queue/trace, deliverability, autoconfig, webmail SSO, quota/archive/abuse contracts.

## V4.0.21.20 — Files / Access / Application Runtime Registry
Deep canonical population for File Manager, SSH/SFTP, Git/deploy, runtimes, dependency tooling, secrets, cron/workers, WordPress, app installer and staging.

## V4.0.21.21 — Operations / Resilience / Security / Observability
Deep canonical population for backup/restore/DR, firewall/WAF/malware, observability/alerts, updates, installer/Doctor, recovery and durable operations.

## V4.0.21.22 — Global Cross-Registry Audit
Audits all populated families together and identifies the final global normalization/traceability blockers before readiness.


## V4.0.21.23 — Global Canonical Registry Consolidation
Establishes authoritative global registry schemas and consolidates canonical resource/operation/error identifiers. Row-level completeness C1–C7 remains required before GLOBAL READY.
