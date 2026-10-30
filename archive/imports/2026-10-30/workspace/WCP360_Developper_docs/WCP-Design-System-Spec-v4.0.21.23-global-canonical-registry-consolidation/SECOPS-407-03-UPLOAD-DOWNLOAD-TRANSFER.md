# V4.0.7 --- Upload, Download & Transfer Contract

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

## Upload

Multipart/chunked/resumable seulement si backend certifié. Session
d'upload : target, expected size, received chunks, checksum optionnel,
expiry.

## Preflight

Quota bytes/inodes, max file size, path, filename, free host space et
policy malware selon profil.

## Finalisation

Assembler en staging → size/checksum → optional scan → atomic move →
ownership/mode → verify.

## Download

Authorization revalidée au début ; streaming borné ; range requests
capability ; pas de path fourni au serveur de fichiers hors token
scoped.

## Failure

Interrupted upload n'apparaît pas comme fichier final. Cleanup des
temporaires par policy.
