# V4.0.7 --- Fail2ban / CrowdSec Intrusion Prevention

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

Fail2ban jails/bans et CrowdSec scenarios/decisions/bouncers gardent
leurs modèles propres.

## Vue commune

Source, target service, reason/scenario, decision, start/expiry,
evidence et engine.

## Actions

Ban/unban/allowlist seulement via engine adapter et permission. Manual
permanent ban exige justification.

## Safety

Protect management/admin IP recovery paths ; IPv6/CIDR normalization ;
avoid accidental global lockout.

## Tenant

WCP-USER peut voir certains incidents de son scope mais ne contrôle pas
le firewall global.

## Expiry

Temporary decisions preferred; stale decisions reconciled.
