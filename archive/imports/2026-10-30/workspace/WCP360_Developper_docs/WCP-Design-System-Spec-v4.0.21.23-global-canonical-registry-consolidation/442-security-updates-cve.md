# 442 --- Security Updates & CVE Findings

## Objectif

Corréler packages installés et avis de sécurité provenant de sources
configurées.

## Pages

Security Updates; Findings; Affected packages; Remediation jobs; Reboot
required.

## Règle

Un CVE n'est marqué applicable que si le backend/source fournit une
correspondance suffisante ; sinon `potentially affected`.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.

## V4.0.7 normative deepening

- `SECOPS-407-26-SECURITY-UPDATES-CVE-SBOM.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.12 normative deepening

- `LIFE-4012-04-UPDATE-CHANNELS.md`
- `LIFE-4012-05-UPDATE-POLICY-SCHEDULING.md`
- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
