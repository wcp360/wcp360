# ADR-019 --- Audit/Event Retention & Tamper Evidence

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

Audit security/administrative est append-oriented dans PostgreSQL +
export/rotation vers stockage externe optionnel. Les événements métier
et logs techniques ne sont pas confondus avec l'audit.

## Champs

timestamp UTC, real actor, effective actor, tenant, action, resource,
request/job/correlation IDs, result, source, security context et safe
diff/metadata.

## Tamper evidence

Chaînage/hash/checkpoint périodique et export scellé/signé selon
politique ; DB superuser local reste une frontière de confiance
explicitement reconnue.

## Rétention

Valeurs par défaut documentées mais configurables selon
opérateur/juridiction ; purge elle-même auditée.

## Privacy

Pas de secrets ; minimisation des données personnelles ; accès audit
soumis à RBAC/tenant scope.
