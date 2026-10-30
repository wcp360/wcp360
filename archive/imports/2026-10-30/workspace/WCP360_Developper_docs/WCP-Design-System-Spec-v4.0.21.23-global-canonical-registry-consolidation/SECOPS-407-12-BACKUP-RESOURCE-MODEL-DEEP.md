# V4.0.7 --- Backup Resource Model --- Deep Contract

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

## Ressources

`BackupPolicy`, `BackupRun`, `RecoveryPoint`, `BackupEngine`,
`BackupDestination`, `RestoreJob`, `VerificationRun`, `StorageTarget`.

## Baseline

Conforme ADR-011 : orchestration WCP + Restic premier adapter certifié ;
Local/SFTP/S3-compatible V1 ; NFS comme filesystem destination
monté/validé.

## RecoveryPoint

Manifest immuable/logique : scope, components, timestamps,
engine/repository snapshot ref, encryption state, size,
checksums/metadata et verification status.

## Coverage

Files, DB, mail, DNS/config manifests, secrets uniquement via policy
spécifique. Coverage réelle affichée.

## Rule

Successful process exit ≠ valid backup.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
