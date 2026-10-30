# ADR-014 --- Release Artifact Signing & Distribution

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

Chaque release WCP possède manifest immuable, digests SHA-256 ou
supérieur selon politique, signature de release et provenance/SBOM.

## Distribution

Repository HTTPS officiel + mirrors autorisés. La confiance vient de la
signature/root-of-trust de release, pas du mirror.

## Channels

security, stable, preview ; production V1 suit stable + security.

## Keys

Offline/root release key protégée ; clés intermédiaires/rotation et
revocation documentées.

## Apply

Download → verify manifest/signature/digest/provenance policy →
compatibility preflight → stage → migrate/apply → verify →
commit/recover.

## Downgrade

Bloqué si incompatible, retiré ou vulnérable sauf procédure break-glass
documentée.
