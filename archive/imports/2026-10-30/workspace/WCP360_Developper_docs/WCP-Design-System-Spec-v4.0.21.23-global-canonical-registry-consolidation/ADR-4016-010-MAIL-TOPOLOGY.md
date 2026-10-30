# ADR-010 --- Canonical Mail Stack Topology

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

## Décision V1

**Postfix + Dovecot + Rspamd**, avec ClamAV adapter optionnel/recommandé
selon ressources, Sieve via Dovecot/Pigeonhole et webmail comme module
séparé.

## Flux entrant

Postfix → Rspamd policy/content checks → antivirus si activé → Dovecot
LMTP/local delivery → Sieve/mailbox.

## Flux sortant

Submission authentifiée → policy/rate limits → DKIM signing/Rspamd →
Postfix queue → remote MX.

## Auth

Dovecot/auth backend relié aux identités mailbox WCP ; pas aux comptes
administrateurs WCP.

## DNS

SPF/DKIM/DMARC/autoconfig sont générés/observés mais la délivrabilité
reste un état vérifié, pas garanti.

## Abuse

Rate limits par mailbox/domain/tenant, queue inspection,
compromised-account response et réputation restent CORE.
