# ADR-001 --- Linux Distribution & Filesystem Baseline

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

## Contexte

La V4.0.15 exige une distribution Linux, systemd et un filesystem
certifié.

## Options

Debian stable ; Ubuntu LTS ; distributions RHEL-compatible. Pour les
données hosting : XFS ou ext4.

## Décision

**Debian stable 64-bit est la plateforme de référence V1**, sur
installation serveur minimale et systemd. La matrice exacte de versions
supportées est publiée par release WCP, jamais codée comme promesse
perpétuelle.

**XFS est le filesystem recommandé pour `/srv/wcp-data`** lorsque WCP
contrôle le partitionnement, notamment pour quotas/projet et croissance.
**ext4 est supporté** après validation de la matrice de
quotas/attributs. Le root filesystem reste un choix OS compatible.

## Layout

`/opt/wcp` binaries/runtime immuables ; `/etc/wcp` configuration ;
`/var/lib/wcp` état control-plane ; `/var/log/wcp` logs ; `/run/wcp`
sockets/PID runtime ; `/srv/wcp-data` données tenants ;
`/var/backups/wcp` staging local borné si utilisé.

## Rejet

WCP V1 ne promet pas le support universel de toutes distributions.

## Conséquences

La CI de compatibilité commence par Debian ; Ubuntu/RHEL-compatible
deviennent P1 après certification.
