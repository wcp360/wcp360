# V4.0.7 --- SSH User Access, Public Keys & Shell Restrictions

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

## Séparation

SshServerPolicy global ≠ SshUserAccess tenant ≠ SshPublicKey.

## Keys

Formats/algorithms certifiés, fingerprint canonique, duplicate
detection, comment non autoritatif. Private keys jamais demandées.

## Shell

Disabled, SFTP-only, restricted shell ou full shell selon
entitlement/admin policy. Full shell n'implique jamais sudo.

## authorized_keys

Options forcées/deny forwarding selon profile ; WCP génère,
l'utilisateur ne fournit pas une ligne arbitrary options non validée.

## Revocation

Key disable/revoke et account disable distincts.

## Audit

Fingerprint et principal, jamais key material privé.
