# ADR-005 --- Root Daemon IPC, Authentication & Protocol

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

`wcpd` écoute **uniquement sur un Unix domain socket local** sous
`/run/wcp/`, jamais sur TCP public en V1.

## AuthN/AuthZ

Permissions filesystem strictes + peer credentials du socket + identité
du service WCP. Le daemon réapplique une allowlist de capacités ; il ne
fait jamais confiance au seul frontend.

## Protocole

RPC versionné avec messages structurés et schémas stricts. Aucun champ
`command`, `shell`, `args arbitrary` permettant une exécution générique.

## Actions

Chaque méthode correspond à une opération système bornée : user, vhost,
service config, certificate, quota, firewall adapter, etc.

## Sécurité

Timeouts, tailles limites, validation canonique, correlation/job ID,
audit, anti-replay logique lorsque nécessaire.

## Futur cluster

Un agent distant futur utilisera un protocole distinct authentifié mTLS
; il ne transforme pas le socket local en TCP root daemon.
