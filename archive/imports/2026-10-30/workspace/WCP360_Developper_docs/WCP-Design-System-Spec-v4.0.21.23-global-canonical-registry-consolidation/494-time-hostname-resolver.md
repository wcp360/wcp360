# 494 --- Hostname / Resolver / Time Synchronization

## Fonctions

Hostname/FQDN, `/etc/hosts` via modèle gouverné, DNS resolver, timezone,
NTP/Chrony/systemd-timesyncd selon adapter.

## Santé

Clock synchronization, offset lorsque disponible, resolver health,
hostname/FQDN consistency.

## Importance

TLS, logs, cluster, DNSSEC, mail et audit dépendent d'une heure correcte
; une dérive significative devient un finding.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.

## V4.0.3 normative deepening

Voir `SYSTEM-403-18-RESOLVER-HOSTS-NSS.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
