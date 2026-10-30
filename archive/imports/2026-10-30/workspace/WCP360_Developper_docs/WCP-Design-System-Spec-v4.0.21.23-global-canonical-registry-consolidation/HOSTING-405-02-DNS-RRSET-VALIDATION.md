# V4.0.5 --- DNS RRsets, Record Validation & Conflict Rules

## Statut

**NORMATIVE --- V4.0.5 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 2 ---
NO CODE**

Portée : DNS authoritative/providers/DNSSEC → TLS/ACME/certificats →
MySQL/MariaDB → PostgreSQL → Redis/Memcached → outils DB/SSO.

## Invariants

-   `Domain`, `DNSZone`, `RegistrarDomain`, `Certificate`, `Database`,
    `DatabaseUser`, `CacheInstance` restent des ressources distinctes.
-   WCP distingue toujours control-plane desired state, état réellement
    observé et état externe.
-   Les credentials ne sont ni des identifiants de ressource ni des
    données de log/audit.
-   Les adapters externes et moteurs locaux n'acceptent que des
    opérations typées et bornées.
-   Toute opération destructive, rotation de secret ou mutation
    DNS/TLS/DB sensible possède verify/reconcile.
-   WCP-USER n'obtient jamais de primitives root, superuser DB, accès
    inter-tenant ou configuration fournisseur arbitraire.

## Types V1

A, AAAA, CNAME, MX, TXT, SRV, CAA, NS et PTR uniquement lorsque
provider/reverse-zone le permet. SOA géré selon mode.

## RRset

Nom canonique, type, TTL, values structurées. Les contraintes sont
validées par type, pas par simple textarea.

## Conflits

CNAME coexistence, apex constraints/provider capabilities, duplicate
records, invalid MX/SRV priority/weight/port, malformed IP,
label/length, wildcard policy.

## TXT

Le contenu peut contenir SPF/DKIM/verification mais WCP ne prétend pas
comprendre tous les TXT. Taille/quoting/provider encoding doivent être
normalisés.

## TTL

Bornes globales/provider ; effective TTL affiché. Une réduction
temporaire avant migration est un workflow, pas une garantie de
propagation instantanée.
