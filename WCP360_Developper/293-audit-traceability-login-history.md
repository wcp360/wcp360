# 293 --- Audit & Traçabilité avancée

## Origine

**\[V3.1 --- exigence ajoutée par le projet\]** Traçabilité des
modifications et historique des connexions au panneau.

## Objectif

Répondre précisément à : **qui a fait quoi, sur quelle ressource, quand,
depuis quel contexte et avec quel résultat ?**

## Pages

Audit Explorer; Login History; Resource History; Configuration Changes;
Impersonation History; Exports.

## Événement d'audit

Actor réel, actor impersonné éventuel, rôle, scope, action, target,
before/after redacted, timestamp, résultat, source/session, correlation
ID, job ID.

## Historique ressource

Depuis un VHost, domaine, DB, firewall rule, compte ou service, afficher
sa timeline de changements et les diffs disponibles.

## Connexions panel

Succès/échec, méthode d'authentification, 2FA/MFA utilisée, session
créée/révoquée, origine réseau lorsque la politique le permet.

## Intégrité

Les événements d'audit ne sont pas modifiables depuis l'interface
standard. Politique de rétention/export à définir avant production.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
