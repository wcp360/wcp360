# Consolidation des règles mail — 30 octobre 2026

Les règles mail utilisent désormais le même état et le même renderer que les boîtes et les routes d’adresses. La navigation conserve une seule entrée **Email & Webmail**, dont la recherche reconnaît aussi les filtres, réponses automatiques, catch-all et rétention. La baseline visuelle V2.5.3.12 est conservée.

## Parcours disponibles

| Vue | Comportement de démonstration |
|---|---|
| [Filtres et réponses](email.html#rules) | Création, édition, suppression du brouillon, grille commune avec recherche, tri, filtres et export. Filtres littéraux sur sujet/expéditeur ou seuil numérique de spam ; actions dossier, tag, quarantaine ou rejet. |
| [Catch-all et rétention](email.html#retention) | Comparaison brouillon/publié ; rejet des destinataires inconnus ou livraison vers une boîte active ; métadonnées d’archive, corbeille et quarantaine. |
| [Prévisualisation](email.html#trace) | Message inventé ; ordre des filtres, règles d’arrêt, résolution du catch-all, suppression des réponses automatiques et échéances de rétention expliqués. Aucun message envoyé, déplacé ou supprimé. |
| [Activité des politiques](email.html#mail-activity) | Identifiant de job, statut, motif, résultat appliqué ou refusé ; versions antérieures et retour arrière revu. |
| [Configuration client](email.html#setup) | Téléchargement JSON réel des paramètres fictifs ; fiches de clients de démonstration persistantes et révocables. Aucune création de mot de passe ni de jeton OAuth. |

Une réponse automatique impose une boîte active, un sujet, un texte, des dates **UTC** valides et un intervalle entre réponses. Une seule réponse activée est autorisée par boîte. La prévisualisation supprime les réponses aux expéditeurs automatisés/listes, hors calendrier, durant l’intervalle de protection et après rejet/quarantaine.

Les noms sont uniques et les priorités identiques sont interdites sur des périmètres de filtres qui se chevauchent. Les dossiers/tags sont des libellés ; les chemins et traversées de répertoires sont refusés. Les expressions régulières, filtres de corps de message, listes de diffusion et délégations ne font pas partie de ce sous-ensemble.

## Publication et conservation des données

Le stockage existant `wcp360-email-center-v2` est conservé. Une section `mailPolicy` contient `draft`, `published`, révisions, génération, preuve de validation locale, historique et opérations. Les clients de démonstration utilisent `clientGrants` dans ce même état.

1. Enregistrer les changements dans le brouillon.
2. Valider localement ce brouillon avec l’inventaire courant des boîtes.
3. Prévisualiser un message si nécessaire, puis revoir la publication et son motif.
4. Le Job Center exécute une simulation. Seul le job USER correspondant, avec le type exact attendu, peut appliquer le snapshot revu.
5. Un échec, une annulation, un changement de boîtes ou de version publiée conserve la version précédente. Les nouvelles éditions du brouillon pendant l’attente sont conservées séparément.

La publication incrémente une génération une seule fois. Le retour arrière revalide la version retenue contre les boîtes actuelles et crée un autre job. Les réglages catch-all/quarantaine et la liste historique des réponses deviennent des projections de cette version publiée ; les anciens éditeurs concurrents sont retirés.

Les réponses automatiques historiques ne conservaient ni leur texte ni des dates exploitables. Elles deviennent des **brouillons désactivés**, avec une note demandant de compléter ces informations. Aucun texte ni calendrier actif n’est fabriqué. Le catch-all historique vers Contact est conservé lorsque cette boîte est active.

Le contrôleur recharge l’état lors des modifications provenant d’un autre onglet. Les formulaires de politique refusent un brouillon ou un inventaire périmé. Une section de politique mal structurée affiche une réparation explicite avec confirmation typée ; elle n’est pas remise à zéro silencieusement et ne bloque pas l’inventaire des boîtes.

## Consolidation des fichiers et catégories

| Ancienne URL | Destination |
|---|---|
| `mail-rules.html` | `email.html#rules` |
| `mailbox-detail.html` | `email.html#mailboxes` |
| `mail-antispam.html` | `email.html#security` |
| `mail-authentication.html` | `email.html#security` |
| `mail-deliverability.html` | `email.html#trace` |
| `mail-logs.html` | `email.html#trace` |

Les paramètres de requête et ancres fournis sont conservés par les routes de compatibilité. Les anciennes vues de journaux conduisent maintenant à une prévisualisation identifiée comme telle ; aucune collecte SMTP ou observation SPF/DKIM/DMARC n’est prétendue.

Blocs retirés de `hosting-parity.js` : `mail-rules`, `account-operations`, `reseller-overview`, `reseller-governance`. Blocs retirés de `root-control.js` : `accounts`, `databases`, `serverconfig`, `software`. Ces huit définitions n’ont plus de page consommatrice. Les fichiers des moteurs et les routes de compatibilité restent présents.

Les formulaires historiques de réponse et de faux journal de livraison sont retirés du contrôleur Email. Tous les formulaires Email restants passent par la validation commune. Le bouton CSV télécharge un modèle, avec un intitulé explicite ; il ne prétend plus avoir importé des boîtes. Les paramètres client produisent effectivement un fichier JSON.

## Fichiers principaux

- [assets/mail-rules-schemas.js](assets/mail-rules-schemas.js) : validation métier et moteur déterministe de prévisualisation.
- [assets/mail-rules-center.js](assets/mail-rules-center.js) : vues de politiques branchées dans le renderer Email, brouillon/publication, résultats et historique.
- [assets/email-center.js](assets/email-center.js) : état unique, navigation interne, contrats de compatibilité, formulaires et téléchargements.
- [assets/reference-ui.js](assets/reference-ui.js) : catégorie Email unique, mots-clés de recherche et résolution des anciennes routes.
- [../tools/audit_wcp360_mail_rules.mjs](../tools/audit_wcp360_mail_rules.mjs) : 50 contrôles de politique, prévisualisation et cycle de publication.

## Vérifications

- **425 contrôles de comportement réussis** sur les dix suites, dont 50 pour les nouvelles politiques mail et 33 pour la suite mail/comptes/récupération.
- Intégration : **232 HTML**, **57 JavaScript**, **2 687 références locales**, **64 redirections**, **3 975 assertions** réussies.
- Les pages et nouveaux scripts sont servis avec HTTP 200 par le serveur d’aperçu local.
- Le connecteur navigateur a retourné `The browser access host is unavailable`. Le rendu, les dialogues natifs et le focus réel restent à vérifier dans un navigateur.

## Reste à traiter

Le bilan initial de 115 pages à intégrer descend à **109 pages** : 90 statiques et 19 génériques. Le nombre de centres partiels reste **26** ; Email demeure dans cette catégorie malgré les nouveaux parcours vérifiés. Les écarts I-03 et I-10 du [bilan initial](20-integration-coverage-audit.md) sont traités sur le périmètre observé.

Le provisionnement historique des boîtes doit encore être rapproché du résultat de son job. Les destinations externes restent en attente de vérification ; leurs décisions d’activation ne sont pas encore un cycle complet. Les pages Domaines mail, les mesures réelles de délivrabilité, le transport et les contrats de politique ROOT restent à intégrer. La rétention actuelle ne lance aucune suppression ni archive de messages.

Les prochains lots P0 restent Domaines/Sites/Comptes, accès fichiers, IAM et approbations/audit/notifications. Les documents d’audit antérieurs et leurs empreintes conservent leur valeur de photographie historique, pas de certification de l’état modifié.
