# 101 --- System Identity, OS & Kernel

## Statut et origine

\[SOURCE\] OS & Kernel Info; Hostname & Timezone.

## Objectif

Gérer l'identité système et exposer versions OS/kernel sans transformer
WCP en éditeur arbitraire de fichiers.

## Pages / sous-vues

Overview; Hostname; Timezone; Locale; OS; Kernel; Boot information.

## Ressources et données

hostname, FQDN, timezone, NTP state si disponible, distribution, kernel,
architecture, boot time.

## Actions opérateur

Modifier hostname/timezone; synchroniser heure si capability; ouvrir
documentation système.

## Monitoring, santé et logs

Clock drift si fourni, kernel/version, dernier boot.

## Sécurité, permissions et audit

`system.identity.read/write`; avertir des impacts DNS/TLS/services.
Audit des changements.

## Contrat backend attendu

Typed system identity endpoints; validation backend; effective value
after apply.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Après modification, la valeur effective est relue du système.

## V4.0.3 — Deep Specification normative

Ce document de surface doit être interprété avec les contrats approfondis suivants :

- `SYSTEM-403-02-HOST-IDENTITY-BOOT-KERNEL.md`
- `SYSTEM-403-03-TIME-NTP-LOCALE.md`
- `SYSTEM-403-14-SYSCTL-KERNEL-TUNABLES.md`

Les règles V4.0.3 prévalent lorsqu'une ancienne formulation est moins précise. Le statut de cette surface reste **à valider contre le READY Gate System/OS**.
