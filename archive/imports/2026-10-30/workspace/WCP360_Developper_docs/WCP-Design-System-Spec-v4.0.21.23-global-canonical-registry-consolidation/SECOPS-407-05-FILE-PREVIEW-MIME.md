# V4.0.7 --- File Preview, MIME & Active Content Safety

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

## Preview

Texte, images et formats explicitement allowlistés. MIME sniffing n'est
pas une preuve de sécurité.

## Active content

HTML/SVG/PDF ou formats actifs sont servis dans origine/sandbox non
privilégiée lorsque preview autorisée ; jamais exécutés dans le contexte
du panel.

## Limits

Max preview bytes, dimensions, decompression et parser time.

## Metadata

Nom, type observé, taille, timestamps, owner/mode si permission.

## Download fallback

Format non sûr/non supporté → téléchargement, pas rendu inline
privilégié.
