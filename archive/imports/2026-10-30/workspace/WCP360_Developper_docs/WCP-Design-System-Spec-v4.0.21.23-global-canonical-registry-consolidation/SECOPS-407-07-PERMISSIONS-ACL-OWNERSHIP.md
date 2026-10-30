# V4.0.7 --- Permissions, ACL, Ownership & Recursive Changes

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

POSIX mode baseline ; ACL capability si filesystem/support. Owner/group
changes beaucoup plus restreints que chmod.

## WCP-USER

Ne peut attribuer un fichier à root/autre tenant ni ajouter groupe
donnant accès cross-tenant.

## Recursive

Preview count/impact, max entries, symlink behavior et mount boundary.
Job pour gros arbres.

## Safe presets

Web-readable, private, executable-script profiles peuvent guider sans
masquer valeur effective.

## Setuid/setgid/sticky

Bits sensibles policy-gated ; setuid tenant désactivé/interdit selon
baseline.

## Verify

Readback metadata après apply.
