# MASTER V4.0.7 --- Files, Backup, Security & Recovery

## Architecture

HostingAccount → FileRoot → FileEntry / EditorSession / ArchiveOperation
/ SFTP access.

BackupPolicy → BackupRun → RecoveryPoint → VerificationRun → RestoreJob,
via BackupEngine + BackupDestination.

Security Center agrège SecurityFindings issus de scanner malware,
intrusion prevention, WAF, updates/CVE/SBOM, SSH et autres contrôles,
sans confondre evidence déterministe et recommandation IA.

## Principes majeurs

-   Le tenant ne manipule jamais de chemin absolu.
-   PathResolver et isolation filesystem sont des frontières de
    sécurité.
-   Upload finalisé seulement après validation/quotas/scan policy.
-   Archive extraction est hostile par défaut : Zip Slip, symlinks,
    bombs et inodes sont traités.
-   Private SSH keys ne sont jamais collectées.
-   SSH server changes ont anti-lockout.
-   Backup valide signifie recovery point + manifest + rétention +
    capacité de restore vérifiée.
-   Un backup local sur le même failure domain n'est pas du DR complet.
-   Scanner indisponible ≠ clean.
-   Quarantine ≠ delete.
-   AI recommendation ≠ authorization.
-   Recovery après compromission suit containment/evidence/credential
    rotation/rebuild-or-restore/verification.

## Prochaine passe

V4.0.8 doit auditer en profondeur **Applications & Developer Platform**
: WordPress Toolkit/CMS, auto-installer, Git/CI-CD/webhooks,
Composer/npm/pip, Node.js/Python/Ruby/Go managers, process
supervisors/workers, environment/secrets, staging/cloning,
dependency/supply-chain security et éventuellement containers selon le
scope déjà présent.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
