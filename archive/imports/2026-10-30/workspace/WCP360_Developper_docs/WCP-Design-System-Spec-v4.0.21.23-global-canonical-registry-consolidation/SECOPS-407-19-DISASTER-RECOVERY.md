# V4.0.7 --- Disaster Recovery & Bare/Control-plane Recovery

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

## Distinction

Tenant restore ≠ node recovery ≠ WCP control-plane recovery ≠ full
disaster recovery.

## DR Plan

Dependencies, OS/bootstrap prerequisites, WCP state/config/secrets,
hosting data, DNS/TLS/mail/DB, backup repository access et validation
order.

## RPO/RTO

Targets déclaratifs par profile ; WCP mesure evidence lorsque possible
mais ne promet pas un RTO non testé.

## Bootstrap

Recovery credentials/offline documentation et break-glass governance
hors dépendance unique au serveur détruit.

## Drill

Periodic recovery exercise avec timestamp, environment, recovered scope,
failures et remediation.
