# V4.0.7 --- FileRoot, Path Resolution & Confinement

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

## Modèle

`FileRoot` définit scope, backing path interne, account owner, policy et
capabilities. `FileEntry` utilise uniquement un chemin relatif
canonique.

## Résolution

decode/normalize → reject NUL/control anomalies → relative-path
validation → join sous FileRoot → canonical parent checks → symlink
policy → mount boundary policy → authorization → operation.

## Interdits

`..` escape, absolute path, alternate separator bypass, encoded
traversal, proc/sys/dev escape, bind-mount surprise et accès aux paths
WCP/control-plane.

## TOCTOU

Les opérations sensibles utilisent primitives/fd-relative ou garanties
équivalentes de l'adapter afin de réduire les races entre validation et
ouverture.

## Evidence

Audit conserve FileRoot ID + relative path normalisé, jamais un path
externe non nécessaire.
