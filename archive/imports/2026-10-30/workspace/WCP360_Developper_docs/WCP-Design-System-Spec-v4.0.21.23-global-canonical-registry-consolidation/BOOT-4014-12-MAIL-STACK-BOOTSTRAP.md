# V4.0.14 --- Mail Stack Bootstrap Profile

## Statut

**NORMATIVE --- V4.0.14 INSTALLATION, BOOTSTRAP, FIRST-RUN,
FINALIZATION, REPAIR & SERVER LIFECYCLE COMPLETENESS --- NO CODE**

Cette passe ferme la dernière grande surface opérationnelle identifiée
après V4.0.13 : faire passer une machine supportée d'un état brut à un
WCP exploitable, vérifié, récupérable et administrable sans dépendre
d'actions root opaques.

## Invariants

-   `installed` ≠ `configured` ≠ `healthy` ≠ `production-ready`.
-   L'installateur et le Repair Center utilisent les mêmes contrats
    typés, validation, audit et reconciliation que le reste de WCP.
-   Aucun wizard ne contourne RBAC, secrets, Jobs, audit ou health
    verification.
-   Une détection automatique n'est jamais traitée comme vérité
    irrévocable : elle produit un état observé vérifiable.
-   Les profils simplifient la configuration sans cacher les
    conséquences.
-   Les opérations destructives, de réparation ou de récupération sont
    prévisualisées et auditées.
-   L'absence volontaire d'un service (mail, DNS local, DB locale, etc.)
    n'est pas une erreur si le profil le permet.
-   Un serveur ne peut pas être déclaré READY si un finding bloquant
    subsiste.

## Modes

Full local mail, outbound relay, external mail, disabled.

## Full

Postfix-equivalent MTA adapter, Dovecot-equivalent IMAP/POP adapter,
Rspamd baseline, antivirus option/policy, Sieve, DKIM, webmail,
autoconfig/autodiscover.

## Identité

Hostname/HELO, PTR, TLS, DKIM selector/key lifecycle.

## Validation

SMTP local, submission/auth, IMAP, queue, filtering pipeline et DNS
readiness.

## Modularité

Mail n'est jamais imposé à un serveur web-only.
