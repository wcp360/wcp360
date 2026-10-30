# V4.0.3 --- Host Hardening Baseline & Security Posture

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Baseline

Le baseline couvre permissions WCP, service sandboxing, firewall
exposure, package signatures, SSH posture, MAC, sysctl registry,
secrets, temp dirs, logging/audit et update posture.

## Profiles

`recommended`, `strict`, `compatibility` peuvent être proposés ; chaque
contrôle indique impact, rationale, applicability et rollback.

## Scanner

Posture scan produit findings `pass/fail/not_applicable/unknown`;
`unknown` n'est jamais compté comme pass.

## Remediation

Automatique seulement pour contrôles WCP-owned et réversibles. Les
changements externes/risqués demandent plan et confirmation.

## Standards

CIS ou autres benchmarks pourront être mappés comme références, sans
prétendre à une certification automatique.
