# V4.0.21.11 --- Backup Resource & Policy Model

## Ressources

`backup_policy`, `backup_run`, `backup_artifact`, `backup_manifest`,
`backup_target`, `backup_retention_policy`, `backup_verification`,
`backup_encryption_key_ref`.

## Portée

Account, site, files, database, mail, DNS/control-plane metadata et
application state selon policy. Une sauvegarde doit indiquer exactement
ce qu'elle contient et ce qu'elle ne contient pas.

## Artifact

Checksum, encryption state, storage target, size, created_at,
expires_at, manifest ref, verification state, immutable/lock metadata
lorsque disponible.

## Règles

-   chiffrement au repos selon policy ;
-   credentials du stockage dans SecretStore ;
-   checksum et manifest signés/fiables ;
-   rétention distincte de suppression client ;
-   aucune sauvegarde annoncée `VERIFIED` sans lecture/validation ;
-   les sauvegardes hors serveur sont une capacité distincte des
    sauvegardes locales.
