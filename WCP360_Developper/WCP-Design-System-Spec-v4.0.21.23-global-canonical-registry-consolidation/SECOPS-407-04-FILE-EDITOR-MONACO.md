# V4.0.7 --- Web Code Editor / Monaco Boundary

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

## UX

Monaco peut être l'implémentation UI, mais le contrat reste
EditorSession.

## Open

Read permission, max editable size, encoding detection, binary
rejection, version fingerprint.

## Save

Client envoie nouvelle version + expected fingerprint → backend
revalidate path/permission → write staging → fsync/atomic replace
lorsque possible → ownership/mode preservation → verify.

## Conflict

Si fichier changé depuis ouverture : `conflict`, diff/refresh/save-as ;
jamais overwrite silencieux.

## Secrets

Policies peuvent bloquer/redacter `.env`, private keys, credential
stores et fichiers système sensibles.

## Pas de terminal

Editor ne fournit ni shell, ni arbitrary command, ni privileged save.
