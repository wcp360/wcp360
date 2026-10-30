# V4.0.5 --- TLS Certificate Canonical Model

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

## Ressources

Certificate, CertificateOrder, CertificateBinding, IssuerAccount et
PrivateKeyReference.

## Identité

Certificate ID WCP distinct du serial X.509 et du private key ID.

## Etats

pending_validation, issuing, issued, deploying, active, renewal_due,
renewing, expired, revoked, failed/degraded.

## SAN

Hostnames normalisés et autorisés par ownership/domain scope. Wildcard
possède règles de validation spécifiques.

## Private keys

Non exportables par défaut ; accès exceptionnel policy/step-up si
fonction retenue. Jamais log/audit/event.

## Binding

Un certificat émis n'est pas forcément déployé. Binding à
Website/mail/service est une ressource séparée et vérifiée.
