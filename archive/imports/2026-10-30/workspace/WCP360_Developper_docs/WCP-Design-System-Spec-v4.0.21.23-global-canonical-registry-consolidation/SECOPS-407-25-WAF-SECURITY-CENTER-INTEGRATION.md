# V4.0.7 --- WAF Findings & Security Center Integration

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

## Boundary

WAF configuration reste Web contract V4.0.4 ; Security Center agrège
findings/events.

## Evidence

Rule ID/profile, website, timestamp, action, source info redacted,
request metadata limitée.

## False positives

Structured exclusion workflow avec scope, reason, expiry et audit ; pas
de disable-global comme remède par défaut.

## Correlation

Repeated WAF events peuvent créer finding mais ne déclenchent pas
automatiquement file deletion/account suspension sans policy.

## Privacy

Bodies/cookies/auth headers ne sont pas copiés dans Security Center.
