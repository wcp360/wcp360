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

## Extension V3.4 --- Systems & Operations Completeness

Ajout des couches Linux network, DNS authoritative, mail deliverability,
telemetry, backup adapters, shared storage, SSH, MFA recovery/SCIM,
Service Accounts/CLI/IaC, bare-metal/Kubernetes extension points, plugin
trust, SIEM et ACME/TLS avancé. Voir
`95-v34-systems-operations-completeness.md` et les trois matrices
MASTER.
