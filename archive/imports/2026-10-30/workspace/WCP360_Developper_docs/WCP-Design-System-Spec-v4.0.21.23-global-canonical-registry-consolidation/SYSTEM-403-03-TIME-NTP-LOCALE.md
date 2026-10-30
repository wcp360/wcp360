# V4.0.3 --- Time, NTP/Chrony, Timezone & Locale

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Ressources

`SystemClock`, `TimeSyncProvider`, `TimezoneConfig`, `LocaleConfig`.

## Observations

UTC time, timezone, synchronization state, source/provider, offset/drift
si fiable, last sync, RTC mode si utile.

## Providers

Chrony, systemd-timesyncd et autres providers certifiés derrière
adapter. Ne jamais configurer deux time daemons actifs sans policy
explicite.

## Mutations

Timezone et NTP servers via modèles structurés. Validation IP/FQDN,
nombre de sources, interdiction de valeurs arbitraires injectées en
config. Apply puis verify.

## Santé

`healthy` si synchronisation prouvée ; `degraded` si drift ou sources
injoignables ; `unknown` si absence de télémétrie fiable.

## Impacts

TOTP, TLS, DKIM/signatures, sessions, scheduler, audit, package
signatures. Un drift important génère une alerte sécurité/opérations.

## DST

L'heure système reste UTC ; les calendriers UI portent une timezone
IANA. Les jobs récurrents documentent les heures répétées/inexistantes.
