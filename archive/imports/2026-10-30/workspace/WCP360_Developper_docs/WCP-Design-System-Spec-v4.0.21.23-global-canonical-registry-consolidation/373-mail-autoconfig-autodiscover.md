# 373 --- Mail Auto-config / AutoDiscover

## Origine

**\[V3.2\]** Configuration automatique clients Outlook/Thunderbird/Apple
Mail.

## Objectif

Générer et servir les mécanismes/configurations compatibles avec la
stack retenue et les domaines hébergés.

## Données

IMAP/POP/SMTP hosts, ports, TLS modes, auth mode, username format,
domain bindings.

## DNS / HTTP

WCP peut générer les enregistrements/endpoints nécessaires selon la
méthode supportée. Les détails exacts sont adapter-driven et testés.

## Validation

Test DNS/endpoints, certificat TLS, cohérence hostname et services.

## Sécurité

Ne jamais embarquer le mot de passe utilisateur dans un profil de
configuration.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.

## V4.0.6 normative deepening

- `MAIL-406-10-AUTOCONFIG-AUTODISCOVER.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
