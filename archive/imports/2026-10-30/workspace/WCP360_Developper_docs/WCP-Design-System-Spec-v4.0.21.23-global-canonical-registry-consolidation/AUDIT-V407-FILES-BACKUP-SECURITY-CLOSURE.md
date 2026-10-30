# V4.0.7 --- Files, Backup, Security & Recovery Closure Audit

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

## Corpus réellement audité

La passe s'appuie sur les fichiers existants File Manager (`29`, `350`,
`363`, `820–823`), FTP/SFTP/SSH (`190`, `194`, `550–554`, `790–795`),
Backup (`210–214`, `420–422`, `530–531`, `780–785`, `ADR-011`), Security
(`184–185`, `290–292`, `440`, `442–443`, `660`, `665`, `668`) et
contrats transversaux.

## Résultat

30 nouveaux contrats normatifs ont été ajoutés. **49 documents
historiques confirmés** ont été reliés à cette profondeur.

## Décisions conservées

-   Restic reste premier adapter certifié de repository selon ADR-011.
-   Destinations V1 : Local, SFTP, S3-compatible ; NFS lorsqu'il est
    monté/validé.
-   Monaco reste un choix d'implémentation UI possible, pas une
    dépendance du contrat.
-   ClamAV/Maldet/Imunify360 restent adapters distincts.
-   CrowdSec et Fail2ban ne sont pas artificiellement fusionnés.
-   SFTP et FTP gardent des backends/policies distincts.

## Non-global-ready

Cette fermeture porte uniquement ces familles. Developer/App runtimes,
Git/CI-CD, containers, billing/provisioning, API/webhooks, observability
et cluster/HA nécessitent encore une passe systématique.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
