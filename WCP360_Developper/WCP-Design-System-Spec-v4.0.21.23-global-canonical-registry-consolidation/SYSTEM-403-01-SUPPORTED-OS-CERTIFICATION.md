# V4.0.3 --- Supported OS, Distribution & Certification Matrix

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

## Objectif

Séparer **détecté**, **supporté** et **certifié** pour les distributions
Linux et leurs versions.

## Profils

Le V1 doit prévoir des profils distincts pour Debian-family et
RHEL-family. Une distribution dérivée n'hérite pas automatiquement du
statut Certified. Chaque profil décrit package manager, repositories,
service manager, firewall backend, MAC framework, chemins standards,
PHP/web/mail packaging et conventions de logs.

## Lifecycle

`unknown → detected → supported → certified`, avec états `deprecated`,
`end_of_support`, `blocked`. La certification est versionnée avec la
version WCP.

## Preflight

OS ID/version, architecture, kernel, init/service manager, filesystem
racine, package database, espace libre, heure, resolver, hostname/FQDN,
SELinux/AppArmor, cgroups et capacités kernel.

## Politique

WCP peut fonctionner en mode diagnostic/read-only sur un hôte détecté
mais non certifié ; les mutations risquées restent bloquées. Une mise à
niveau majeure de distribution nécessite un workflow dédié, jamais un
simple bouton d'update.

## Tests

Fresh install, upgraded install, minimal image, missing package
metadata, EOL release, unsupported derivative, partial repositories,
read-only root et reboot-required.
