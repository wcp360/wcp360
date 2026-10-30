# V4.0.3 --- Package Managers, Repositories & Transaction Safety

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

## Adapters

APT/dpkg et DNF/RPM sont des adapters distincts derrière ressources
`Package`, `Repository`, `Advisory`, `PackageTransaction`.

## Repositories

URI, suite/channel, components, enabled, priority, signing-key
reference, ownership (`os`, `operator`, `wcp`, `third-party`). WCP ne
réécrit pas un repo externe qu'il ne possède pas.

## Signatures

Refuser par défaut repository non signé ou mécanisme désactivant
globalement la vérification. Key rotation et expiration visibles.

## Transaction

Refresh metadata → solve/preflight → impact plan
(install/remove/upgrade/downgrade) → maintenance constraints → execute →
verify DB/package state → detect reboot/service restart requirements.

## Locks

Respecter les locks natifs du package manager. Ne jamais supprimer un
lock uniquement parce qu'il existe ; déterminer le
propriétaire/processus et attendre/échouer proprement.

## Échecs

dpkg/rpm database inconsistent, interrupted transaction, disk full, repo
timeout, signature failure, dependency conflict et partial configuration
sont des états distincts.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
