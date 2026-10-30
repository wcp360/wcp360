# 294 --- 2FA / MFA & Access Security

## Origine

**\[V3.1 --- exigence ajoutée par le projet\]** MFA pour le panneau et
politiques renforcées pour accès privilégiés SSH/FTP.

## Objectif

Centraliser la politique MFA sans confondre authentification WCP et
protocoles système.

## Pages

MFA Policy; User Factors; Recovery; Trusted/Active Sessions; Step-up;
SSH/FTP Access Policies.

## Facteurs

TOTP et WebAuthn/passkeys si le backend d'identité les supporte. Les
méthodes réellement disponibles sont capability-driven.

## Politiques

Required for admins; required for selected roles; enrollment grace
period si retenu; recovery workflow; step-up pour actions critiques.

## SSH / FTP

WCP peut imposer des politiques d'accès (par exemple clés SSH plutôt que
mot de passe) mais ne doit pas prétendre que chaque protocole FTP
supporte le même MFA que le panneau.

## Audit

Enrollment, factor removal, recovery, policy change et step-up sont
audités.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
