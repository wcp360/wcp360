# V4.0.3 --- Linux Users, Groups, UID/GID & Protected Accounts

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

## Catégories

System accounts, WCP service accounts, hosting accounts,
application/runtime accounts et human shell accounts sont distincts.

## Identité

Username ≠ resource ID. UID/GID sont observés et peuvent varier entre
hôtes ; ils ne servent pas d'identité control-plane.

## Allocation

Pools/ranges réservés par profil OS. Détecter collision UID/GID, compte
existant hors WCP, home déjà présent et groupes incompatibles.

## Protected accounts

root et comptes système critiques sont marqués protected par policy/OS
adapter. Rename/delete/shell change interdits via opérations ordinaires.

## Création

Validate → reserve logical identity → allocate UID/GID → create
group/user → home selon policy → ownership → shell/access policy →
verify NSS/getent → audit.

## Suppression

Impact preview sur processus, fichiers, cron, mail, DB, runtime et
backups. Jamais `userdel -r` conceptuellement aveugle. Données et compte
peuvent avoir des lifecycles séparés.

## NSS

Prévoir local files et, ultérieurement, external NSS sans supposer que
tous les comptes résident dans `/etc/passwd`.
