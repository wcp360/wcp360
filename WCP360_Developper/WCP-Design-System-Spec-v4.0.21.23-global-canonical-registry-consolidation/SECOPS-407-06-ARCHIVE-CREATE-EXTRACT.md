# V4.0.7 --- Archive Creation & Extraction

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

## Formats

ZIP, TAR, TAR.GZ selon adapters. Format capability explicite.

## Extraction preflight

Lister entries lorsque possible ; reject absolute paths, `..`, device
nodes, unsafe links, excessive entry count, expanded size et ratio bomb.

## Destination

Toujours sous FileRoot autorisé ; collision policy explicite ; quota
bytes/inodes vérifié pendant extraction.

## Symlinks

Policy restrictive ; une archive ne crée pas un lien permettant
l'évasion du FileRoot.

## Create

Sources scopées, manifest, max size et temp-space check.

## Failure

Partial extraction produit inventaire et cleanup/reconcile policy.
