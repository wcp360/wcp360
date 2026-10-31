# V4.0.7 --- V4.0.7 READY Gate & Remaining Gaps

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

## Fermé au niveau SPECIFIED-DEEP

File Manager/editor/preview/archive/permissions, path/symlink/mount
security, SFTP/FTP, SSH user/server, backup
engine/policies/destinations/encryption/retention/verification/restore/DR,
Security Center, malware/quarantine, Fail2ban/CrowdSec, WAF findings,
updates/CVE/SBOM, compromise recovery et cross-tenant tests.

## Critères

No absolute tenant paths, no traversal/symlink escape, backup restore
verified, repository secrets protected, anti-lockout SSH, scanner
unknown≠clean, quarantine reversible, no automatic AI deletion, security
findings evidence-based, DR drillable.

## Statut

**V4.0.7 = SPECIFIED-DEEP. Pas GLOBAL READY.**

Les familles Applications/Developer tooling, Git/CI/CD, runtimes
Node/Python/Ruby/Go, containers, billing/provisioning/API/webhooks,
observability et cluster/HA doivent encore être ré-auditées/approfondies
avant fermeture globale.
