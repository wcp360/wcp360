# V4.0.7 --- File Entry Operations & Conflict Semantics

## Statut

**NORMATIVE --- V4.0.7 FILES, BACKUP, SECURITY & RECOVERY DEEP
SPECIFICATION --- NO CODE**

Cette passe approfondit les familles File Manager, transferts,
sauvegarde/restauration, sécurité hôte/tenant et récupération déjà
présentes dans le corpus V4.0.6. Elle respecte notamment `650–669`,
`780–795`, `820–823`, `ADR-011` et les contrats System/OS.

## Invariants

-   Toute opération fichier est relative à un `FileRoot` résolu côté
    backend ; l'API tenant n'accepte jamais un chemin Linux absolu.
-   Canonicalisation, ownership, symlinks, mounts, ACL et quotas sont
    vérifiés côté serveur avant mutation.
-   Un transfert/copie n'est pas un backup sans manifest, recovery
    point, rétention et restaurabilité vérifiable.
-   WCP orchestre ; les moteurs concrets sont des adapters/capabilities
    certifiés.
-   WCP-USER n'obtient jamais shell root, private SSH key, scanner root
    primitive, firewall primitive ni lecture inter-tenant.
-   Une détection malware n'autorise pas une suppression automatique
    irréversible.
-   Restore, quarantine release, destructive purge et changements SSH
    critiques sont auditables et protégés.

## Actions

create file/dir, rename, move, copy, delete, trash/restore si
capability, calculate size et metadata refresh.

## Conflits

`fail`, `skip`, `rename`, `overwrite` sont explicites. Overwrite vérifie
type/cible et peut exiger version/precondition.

## Atomicité

Rename atomique lorsqu'un même filesystem le permet. Cross-filesystem
move devient copy+verify+delete avec état partiel possible.

## Delete

File vs directory, recursive delete, symlink et mountpoint sont
distingués. Suppression récursive exige preview/limits.

## Concurrency

ETag/generation ou metadata precondition pour éviter l'écrasement
silencieux.
