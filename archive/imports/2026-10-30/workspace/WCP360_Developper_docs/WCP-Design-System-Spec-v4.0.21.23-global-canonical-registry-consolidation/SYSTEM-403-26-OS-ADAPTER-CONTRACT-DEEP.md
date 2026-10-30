# V4.0.3 --- OS Adapter Contract --- Deep Specification

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

## Responsabilité

L'adapter OS traduit une intention WCP typée vers les primitives de la
distribution. Il ne porte ni RBAC ni logique commerciale.

## Capabilities

identity/time, users/groups, packages/repos, services, filesystem/mount
inventory, firewall, MAC, resolver, reboot-required, logs et resource
control selon support.

## Opérations conceptuelles

`discover`, `plan`, `apply`, `verify`, `observe`, `reconcile`. Toute
opération retourne evidence structurée et erreurs normalisées.

## Prohibition

Pas de méthode générique `run(command,args)`,
`write_any_file(path,content)` ou `systemctl(unit_from_client)` exposée
au control-plane.

## Versioning

Adapter versionné par contrat WCP et matrice OS. Une nouvelle
distribution ne devient pas supportée uniquement parce que les commandes
semblent similaires.
