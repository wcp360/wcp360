# ADR-012 --- Backup Format, Encryption & Provider Baseline

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

WCP définit un **manifest versionné** séparant metadata, file datasets,
DB dumps/snapshots logiques et mail datasets. Les archives sont
chunkées/streamées selon adapter, compressées et chiffrées côté WCP
avant remote storage lorsque la politique l'exige.

## Crypto

AEAD via bibliothèque maintenue ; clés référencées via
secret/root-of-trust. Checksums par objet/chunk et manifest
signé/authentifié.

## Providers V1

Filesystem/local staging + **S3-compatible object storage** comme
premier remote adapter.

## Restore

Granularité account/domain/site/files/DB/mail lorsque le dataset le
permet. Restore always verifies destination/conflicts.

## Rétention

GFS/simple policies exprimées indépendamment du provider.

## Interdit

Considérer un upload réussi comme restore-ready sans validation
d'intégrité.
