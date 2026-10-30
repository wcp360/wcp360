# V4.0.7 --- SSH Server Hardening & Anti-lockout

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

## Changements critiques

Port/listen, root login, password auth, PAM, allowed users/groups, host
keys et SFTP subsystem.

## Pipeline

Validate candidate → ensure recovery path → firewall coordination →
stage → `sshd` config test via adapter → reload → new-session probe →
commit.

## Rollback

Timer/out-of-band recovery lorsque techniquement possible pour
changements pouvant couper l'accès.

## Root

Root remote login policy WCP-ADMIN only ; no WCP-USER control.

## Host keys

Rotation multi-key overlap, fingerprints et known-host impact
documentés.
