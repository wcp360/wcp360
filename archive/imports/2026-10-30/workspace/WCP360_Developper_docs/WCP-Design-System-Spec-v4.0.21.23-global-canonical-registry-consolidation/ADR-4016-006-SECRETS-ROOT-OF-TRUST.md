# ADR-006 --- Secret Store & Root of Trust

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

Secrets applicatifs chiffrés au repos dans le control-plane ; **clé
maîtresse locale hors DB**, fichier root-only sous `/etc/wcp/keys/` avec
permissions strictes, générée par CSPRNG lors du bootstrap.

## Envelope encryption

DEK par secret/objet ou lot borné ; KEK dérivée/protégée par la root
key. Algorithmes fournis par bibliothèque cryptographique maintenue, pas
de crypto maison.

## Références

Les tables et Jobs stockent `secret_ref`, jamais le secret en payload
durable lorsque évitable.

## Rotation

Version de clé, rewrap, dual-read pendant rotation contrôlée, audit sans
valeur secrète.

## Backup

La stratégie de recovery du root-of-trust est séparée, chiffrée et
explicitement testée ; perdre la clé ne doit pas être masqué.

## Futur

TPM/HSM/Vault/KMS deviennent adapters P1/P2.
