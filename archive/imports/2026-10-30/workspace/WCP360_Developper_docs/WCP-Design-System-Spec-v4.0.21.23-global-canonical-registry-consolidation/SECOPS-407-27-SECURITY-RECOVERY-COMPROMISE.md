# V4.0.7 --- Compromise Containment & Recovery Workflow

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

## Phases

Detect → validate → contain → preserve evidence → eradicate → credential
rotation → restore/rebuild → verify → monitor → close.

## Containment

Account/site/mail/SSH/outbound/network controls chosen by blast radius;
avoid destroying evidence unnecessarily.

## Evidence

Audit references/log snapshots/hashes where policy allows. WCP is not a
full forensic suite unless future capability.

## Rebuild vs clean

For high-confidence system compromise, clean/rebuild from trusted
baseline may be safer than deleting detected files; operator decision
explicit.

## Credentials

Rotate affected WCP, SSH, DB, mail, API, backup/provider secrets
according dependency graph.

## Restore

Use verified recovery point and re-scan before production.
