# ADR-007 --- Nginx/PHP-FPM Configuration Ownership & Generation

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

WCP possède **uniquement ses fichiers générés**, jamais la configuration
globale arbitraire de l'opérateur.

## Générations

Desired model → render dans génération temporaire → syntax test → atomic
switch → reload → functional verify → commit. Dernière génération valide
conservée.

## Nginx

Includes WCP dans namespace/path dédié. Pas d'édition regex d'un fichier
système monolithique.

## PHP-FPM

Pools par tenant/site selon politique ; templates versionnés ; socket
ownership explicite ; limites ressources générées depuis
plan/capabilities.

## Custom directives

Allowlist/typed settings d'abord. Raw snippets seulement dans une
surface avancée explicitement risquée, validée et bornée.

## Drift

Fingerprint + observed parse ; drift externe ne doit pas être écrasé
silencieusement.
