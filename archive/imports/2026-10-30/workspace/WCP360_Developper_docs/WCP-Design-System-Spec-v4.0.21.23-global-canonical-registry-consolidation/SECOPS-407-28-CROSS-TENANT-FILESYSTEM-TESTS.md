# V4.0.7 --- Cross-tenant Filesystem Security Test Matrix

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

## Negative tests

Absolute path, `../`, URL-encoded traversal, Unicode/path normalization
edge, symlink escape, hardlink escape, race swap, bind mount, archive
Zip Slip, recursive chmod into foreign mount, upload overwrite via
symlink, editor stale overwrite, SFTP chroot escape, backup restore into
foreign account.

## Expected

Request rejected or safely contained; no foreign metadata/content
leakage; audit without sensitive disclosure.

## Identity

Tests cover different UID/GID, same reseller/different account, admin
impersonation and suspended account.

## Scale

Large directory trees and inode exhaustion test failure behavior.
