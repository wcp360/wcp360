# Cycle de vie des Sites, Comptes et Archives — 30 octobre 2026

Les changements de ressources sont désormais liés à un **job précis**, puis appliqués au modèle après réussite. Le périmètre reste une simulation locale de métadonnées dans le navigateur. Le shell et la baseline visuelle V2.5.3.12 sont conservés.

## Parcours intégrés

| Centre | En attente | Après réussite | Échec ou annulation |
|---|---|---|---|
| [Création d’un site](websites.html) | Nouveau record `Provisioning` | `Online` en production, `Protected` en staging/développement | Record conservé avec `Provisioning failed/cancelled` ; reprise revue possible |
| Configuration d’un site | `Updating`, hostname et racine appliqués conservés | Hostname et racine virtuels revus | Valeurs précédentes conservées |
| Clone de site | Nouveau record `Provisioning` | Copie des métadonnées de workload uniquement | Record de tentative conservé ; aucune donnée copiée |
| Suppression d’un site | Record visible en `Deleting` | Retrait du record | Record précédent conservé |
| [Création d’un compte](hosting-accounts.html#accounts) | `Provisioning` | `Active` | Record conservé et reprise explicite possible |
| Placement/capacité/add-on | `Updating`, valeurs appliquées conservées | Application des valeurs revues | Valeurs précédentes conservées |
| Suspension/réactivation | `Suspending` / `Resuming` | `Suspended` / statut précédent `Active` ou `Demo` | Statut et page par défaut précédents conservés |
| Résiliation | `Terminating`, aucune nouvelle archive | `Terminated` ; archive finale de métadonnées si demandée | Aucune archive finale créée |
| [Vérification d’archive](hosting-accounts.html#recovery) | `Verifying` | `Restorable` | État précédent conservé |
| Restauration | `Restore queued`, aucun compte recréé immédiatement | Compte recréé/restauré et archive `Restored` | Aucun compte ajouté ; archive précédente conservée |
| Purge d’archive | Archive présente en `Purge queued` | Retrait du record | Archive conservée |

Les nouvelles vues [Activité des Sites](websites.html#activity) et [Activité des Comptes](hosting-accounts.html#activity) exposent la ressource, le type d’action, le job et le résultat réellement appliqué ou refusé.

## Mécanisme commun

[assets/resource-lifecycle.js](assets/resource-lifecycle.js) fournit un moteur réutilisable avec recettes fermées. Il conserve le snapshot revu, l’état précédent, l’identifiant de ressource, l’identifiant et le type du job, son périmètre et une empreinte stable de la ressource en attente.

- Un job d’un autre périmètre, un type différent ou un nom similaire ne peut pas appliquer l’opération.
- Une ressource ne reçoit pas deux opérations simultanées. Les actions sont à nouveau contrôlées à la soumission.
- Une modification de ressource, de source de clone ou d’inventaire gouvernant invalide un résultat périmé.
- Les résultats terminaux ne sont appliqués qu’une fois, y compris après rechargement ou depuis plusieurs instances de contrôleur.
- Une annulation ou un échec conserve les valeurs appliquées ; les modifications récentes extérieures au job sont préservées.
- Si l’enregistrement de la tentative échoue, le job créé est annulé et aucun record nouveau n’est enregistré.

Les anciens records `Provisioning` sans liaison sont conservés. L’action **Retry provisioning** ouvre une revue et crée un nouveau job explicite ; aucun ancien job n’est associé par son libellé. Les autres états historiques dépourvus de liaison ne sont pas convertis automatiquement.

[assets/hosting-lifecycle.js](assets/hosting-lifecycle.js) contient les règles métier des deux portails : schémas communs, limites, unicité des hostnames et racines virtuelles, disponibilités runtime ROOT, transitions et résultats d’archives. Le contrôleur Sites conserve les réglages runtime, maintenance, cache et TLS des centres applicatifs ; une sauvegarde d’onglet ne réécrit plus leur inventaire.

## Compte et récupération : un seul modèle propriétaire

[assets/hosting-model.js](assets/hosting-model.js) devient le propriétaire des comptes, plans de démonstration, serveurs, add-ons, archives et opérations, dans `wcp360-hosting-capacity-v1`.

Les anciennes archives `wcp360-account-recovery-v1` sont reprises lors de la première lecture si le modèle canonique n’en possède pas encore. Cette ancienne clé devient ensuite une projection de compatibilité. Une résiliation réussie et son archive finale sont enregistrées ensemble dans l’état canonique. L’ancien écouteur qui fabriquait une archive dès l’émission d’une opération est supprimé.

L’archive finale conserve le snapshot du compte avant résiliation, une référence déterministe et une rétention de démonstration de 30 jours. Le choix de ne pas conserver d’archive est respecté. La restauration revalide identité, plan et placement ; une collision apparue pendant l’attente bloque la recréation. Un legal hold bloque la purge et reste conservé s’il est ajouté pendant son attente.

Les archives historiques qui ne possèdent pas de snapshot complet ouvrent une revue explicite des paramètres de compte. Le prototype ne prétend pas reconstituer des fichiers à partir de ces seuls records.

## Consolidation des fonctions et liens

- L’éditeur de plans concurrent de Hosting Accounts est retiré ; Plans ouvre [Identity & Access](users-roles.html#plans).
- Les add-ons utilisent les formulaires et validateurs communs. L’option de projet non appliquée est retirée ; le sous-ensemble concerne le compte entier.
- La création d’identités d’accès depuis Sites est retirée ; les anciens records restent consultables et les actions ouvrent [File Access](file-access-center.html).
- La purge de cache générique est remplacée par un lien vers les [préférences web](web-compatibility.html#options).
- Le faux aperçu « site ouvert » est remplacé par l’ouverture du centre applicatif du site.
- Les anciennes actions génériques d’import et de sauvegarde de Sites ouvrent leurs centres propriétaires. Le clone annonce clairement qu’il copie des métadonnées, sans fichiers, bases, secrets, certificats ou releases.
- Le champ Index files sans effet et les formulaires historiques concurrents de Sites sont retirés. Les hostname/racines passent par les formulaires communs.

## Vérification

Depuis la racine du workspace, avec Node disponible :

```powershell
node tools/audit_wcp360_resource_lifecycle.mjs
node tools/audit_wcp360_hosting.mjs
node tools/audit_wcp360_delivery.mjs
node tools/audit_wcp360_integration.mjs
node tools/audit_wcp360_design_system.mjs "WCP360_Design_System"
```

Les onze suites de comportement ont été réexécutées : **494 contrôles réussis**, dont 63 pour le moteur partagé, 35 pour mail/comptes/récupération et 49 pour livraison/Sites. Les tests exercent les modèles et contrôleurs réels avec des adaptateurs UI ; ils ne constituent pas une mesure de couverture de chaque branche ou une vérification de rendu.

L’intégration contrôle **232 HTML**, **60 JavaScript**, **2 697 références locales**, **64 redirections** et **3 985 assertions** : réussi. L’audit de structure ne remonte aucun écart. Les pages Sites/Comptes et leurs nouveaux scripts répondent HTTP 200 sur le serveur local.

Le connecteur navigateur a retourné `The browser access host is unavailable`. Le rendu, les dialogues natifs et le focus réel restent donc à vérifier visuellement.

## Limites et suite du travail

L’écart **I-04** du [bilan initial](20-integration-coverage-audit.md) est traité pour les parcours ci-dessus. Les 109 pages encore statiques ou génériques ne diminuent pas pendant ce lot : il approfondit les centres existants, qui restent classés partiels.

Le moteur ne crée pas de site ou de compte système, ne restaure aucun octet, n’alloue aucune capacité physique et ne suspend pas réellement les services hébergés. Les flags DNS/AutoSSL/mail du compte sont conservés comme choix de démonstration, sans provisionnement automatique dans tous les centres USER. La validité cryptographique des archives et l’intégration des politiques ROOT de rétention restent hors de ce sous-ensemble.

Les prochains lots restent Domaines, accès fichiers, IAM et gouvernance approbations/audit/notifications. Les inventaires et audits antérieurs conservent leur valeur de photographie historique.
