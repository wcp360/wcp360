# WCP360 — App Center 360 : catalogue candidat

Date : 2 novembre 2026. Statut : PROPOSITION / NON CERTIFIÉ / AUCUNE INSTALLATION EFFECTUÉE.

Ce fichier contient 410 références : applications, services, runtimes, modèles de projets, extensions et bibliothèques.
Il reprend les références fournies par l'utilisateur, normalise les noms et propose des compléments. Il ne constitue ni
un inventaire de logiciels déjà installés ni une promesse de support. Les libellés « Installed » et « Total 191 »
du texte d'origine ne sont pas des états WCP360 vérifiés. Les versions, licences, maintenances, architectures et
dépendances doivent être contrôlées pour chaque recette avant publication. Une source de repérage ne certifie pas une recette.

## Choix d'architecture proposés
Pour les applications PHP généralistes : Nginx → Apache → PHP-FPM.
Pour les applications persistantes : frontal Nginx → déploiement isolé par application, suivant la méthode amont.
FrankenPHP reste un profil opt-in, testé par application et version ; les workers ne sont pas activés universellement.
Les services, agents, CI, VPN et accès hôte restent soumis à une politique distincte.

Le catalogue public et son client d'installation doivent rester séparés des services privés WCP360 :
signature, publication, licences centrales, builds et administration de distribution. Aucune extension interne privée
n'entre dans les ZIP publics. Ce catalogue ne modifie pas les baselines et n'attribue pas de nouveaux identifiants canoniques.

## Lecture des profils
- **PHP** : Application PHP candidate au profil Nginx → Apache → PHP-FPM ; recette et dépendances à tester.
- **DEPLOY** : Déploiement applicatif isolé, par image/Compose ou recette officielle ; pas un simple dépôt de fichiers.
- **ISOLE** : Déploiement à risque renforcé : secrets, réseau, outils et permissions strictement bornés.
- **CPU-GPU** : Profil IA selon matériel, modèle et backend ; capacités mesurées, pas de promesse universelle.
- **SERVICE** : Service de données ou d'infrastructure, installé par ADMIN et éventuellement délégué.
- **EXTENSION** : Composant d'une application ou base existante ; pas une application autonome.
- **CLUSTER** : Recette multi-nœuds avec tests de panne, sauvegarde et bascule.
- **INFRA** : Infrastructure réservée à WCP-ADMIN ; aucun accès système implicite pour les tenants.
- **RUNTIME** : Environnement d'exécution à sélectionner par projet, géré par ADMIN.
- **TEMPLATE** : Modèle de projet : création ou import Git, build, puis déploiement de production.
- **DEPENDANCE** : Bibliothèque de projet ; ne doit pas apparaître comme application autonome installable.
- **BUILD-STATIQUE** : Construction isolée puis publication des fichiers statiques.
- **BUILD** : Outil de construction ; ne pas exposer le serveur de développement en production.
- **QUALIFIER** : Identité ou mode non résolu : ne pas proposer le bouton Installer.
- **LAB** : Laboratoire privé ; ne pas offrir comme application de production.
- **VM** : Machine virtuelle ou nœud dédié ; droits, matériel et licence à valider.

## Qualification avant publication
Identifier l'éditeur et l'édition ; vérifier licence et redistribution ; figer version et empreinte ;
documenter dépendances, ressources, ports, réseau, données et secrets ; préparer installation et désinstallation ;
tester installation, mise à jour, sauvegarde, restauration, sécurité, isolation et échecs.
Le retour arrière d'une image ne remplace pas la restauration d'une base migrée.
Aucun accès générique shell.exec ni import privilégié de Compose par WCP-USER.


## Sites, blogs et CMS

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| WordPress | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Drupal Core | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Drupal CMS | Application | PHP | USER | À prioriser | CANDIDAT |
| Joomla | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| b2evolution | Application | PHP | USER | À prioriser | CANDIDAT |
| Dotclear | Application | PHP | USER | À prioriser | CANDIDAT |
| Geeklog | Application | PHP | USER | À prioriser | CANDIDAT |
| PluXml | Application | PHP | USER | À prioriser | CANDIDAT |
| Serendipity | Application | PHP | USER | À prioriser | CANDIDAT |
| Textpattern | Application | PHP | USER | À prioriser | CANDIDAT |
| Backdrop CMS | Application | PHP | USER | À prioriser | CANDIDAT |
| ClassicPress | Application | PHP | USER | À prioriser | CANDIDAT |
| CMS Made Simple | Application | PHP | USER | À prioriser | CANDIDAT |
| Composr CMS | Application | PHP | USER | À prioriser | CANDIDAT |
| Concrete CMS | Application | PHP | USER | À prioriser | CANDIDAT |
| Contao | Application | PHP | USER | À prioriser | CANDIDAT |
| GetSimple | Application | PHP | MASQUE | À prioriser | CANDIDAT |
| Grav | Application | PHP | USER | À prioriser | CANDIDAT |
| LEPTON | Application | PHP | USER | À prioriser | CANDIDAT |
| Microweber | Application | PHP | USER | À prioriser | CANDIDAT |
| MODX | Application | PHP | USER | À prioriser | CANDIDAT |
| Omeka Classic | Application | PHP | USER | À prioriser | CANDIDAT |
| phpwcms | Application | PHP | USER | À prioriser | CANDIDAT |
| Silverstripe CMS | Application | PHP | USER | À prioriser | CANDIDAT |
| SPIP | Application | PHP | USER | À prioriser | CANDIDAT |
| TYPO3 | Application | PHP | USER | À prioriser | CANDIDAT |
| WebsiteBaker | Application | PHP | USER | À prioriser | CANDIDAT |
| WonderCMS | Application | PHP | USER | À prioriser | CANDIDAT |
| Neos | Application | PHP | USER | À prioriser | CANDIDAT |

**GetSimple :** Distinguer le projet d'origine, les forks éventuels et la version retenue ; audit de maintenance préalable.

## Portails et communautés

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| e107 | Application | PHP | USER | À prioriser | CANDIDAT |
| Mahara | Application | PHP | USER | À prioriser | CANDIDAT |
| PHP-Fusion | Application | PHP | USER | À prioriser | CANDIDAT |
| Tiki Wiki CMS Groupware | Application | PHP | USER | À prioriser | CANDIDAT |
| XOOPS | Application | PHP | USER | À prioriser | CANDIDAT |
| Zikula | Application | PHP | USER | À prioriser | CANDIDAT |
| Admidio | Application | PHP | USER | À prioriser | CANDIDAT |
| Elgg | Application | PHP | USER | À prioriser | CANDIDAT |
| HumHub | Application | PHP | USER | À prioriser | CANDIDAT |
| Jcow | Application | PHP | USER | À prioriser | CANDIDAT |
| pH7Builder | Application | PHP | USER | À prioriser | CANDIDAT |

## Forums

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| phpBB | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| FluxBB | Application | PHP | MASQUE | À prioriser | REVUE RENFORCÉE |
| MyBB | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| mylittleforum | Application | PHP | USER | À prioriser | CANDIDAT |
| Simple Machines Forum | Application | PHP | USER | À prioriser | CANDIDAT |
| Vanilla Forums | Application | PHP | USER | À prioriser | CANDIDAT |
| XMB Forum | Application | PHP | USER | À prioriser | CANDIDAT |
| Flarum | Application | PHP | USER | P0 — à qualifier | CANDIDAT |

## Forums modernes

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Discourse | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| NodeBB | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |

**Discourse :** Conserver la méthode Docker supportée par Discourse ; ce n'est pas une installation PHP. Version et dépendances à figer ensemble.

Source : https://github.com/discourse/discourse/blob/main/docs/INSTALL.md

## Wikis et documentation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| MediaWiki | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| DokuWiki | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| PmWiki | Application | PHP | USER | À prioriser | CANDIDAT |
| phpMyFAQ | Application | PHP | USER | À prioriser | CANDIDAT |
| BookStack | Application | PHP | USER | P0 — à qualifier | CANDIDAT |

## Documentation collaborative

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Wiki.js | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Outline | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Docmost | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |

## Éducation et patrimoine

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Moodle | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Chamilo | Application | PHP | USER | À prioriser | CANDIDAT |
| Gibbon | Application | PHP | USER | À prioriser | CANDIDAT |
| Omeka S | Application | PHP | USER | À prioriser | CANDIDAT |
| SLiMS | Application | PHP | USER | À prioriser | CANDIDAT |
| webtrees | Application | PHP | USER | À prioriser | CANDIDAT |

## Agendas et réservation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| LibreBooking | Application | PHP | USER | À prioriser | CANDIDAT |
| LuxCal | Application | PHP | USER | À prioriser | CANDIDAT |
| WebCalendar | Application | PHP | USER | À prioriser | CANDIDAT |
| Easy!Appointments | Application | PHP | USER | À prioriser | CANDIDAT |

## Chat et assistance instantanée

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| BlaB | Application | PHP | USER | À prioriser | CANDIDAT |
| phpMyChat | Application | PHP | USER | À prioriser | CANDIDAT |
| Live Helper Chat | Application | PHP | USER | À prioriser | CANDIDAT |
| Mibew Messenger | Application | PHP | USER | À prioriser | CANDIDAT |
| Sales Syntax Live Help | Application | PHP | USER | À prioriser | CANDIDAT |

## Communication temps réel

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Synapse | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| TeamSpeak Server | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| screego | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Mattermost | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Rocket.Chat | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Jitsi Meet | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Chatwoot | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## E-commerce

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| PrestaShop | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| AbanteCart | Application | PHP | USER | À prioriser | CANDIDAT |
| CE Phoenix Cart | Application | PHP | USER | À prioriser | CANDIDAT |
| CubeCart | Application | PHP | USER | À prioriser | CANDIDAT |
| LiteCart | Application | PHP | USER | À prioriser | CANDIDAT |
| OpenCart | Application | PHP | USER | À prioriser | CANDIDAT |
| Osclass | Application | PHP | USER | À prioriser | CANDIDAT |
| osCommerce | Application | PHP | USER | À prioriser | CANDIDAT |
| Zen Cart | Application | PHP | USER | À prioriser | CANDIDAT |
| TastyIgniter | Application | PHP | USER | À prioriser | CANDIDAT |
| Open Real Estate | Application | PHP | USER | À prioriser | CANDIDAT |

## E-commerce avec dépendances avancées

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Magento Open Source | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Shopware | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| OroCommerce | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**Magento Open Source :** Édition retenue proposée pour Magento ; figer PHP, SQL, recherche, jobs et autres dépendances par version. Adobe Commerce relève d'une recette et licence distinctes.

Source : https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/system-requirements

## CRM, ERP et ressources humaines

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Dolibarr | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| FrontAccounting | Application | PHP | USER | À prioriser | CANDIDAT |
| OrangeHRM | Application | PHP | USER | À prioriser | CANDIDAT |
| Feng Office | Application | PHP | USER | À prioriser | CANDIDAT |
| Group Office | Application | PHP | USER | À prioriser | CANDIDAT |
| SuiteCRM | Application | PHP | USER | À prioriser | CANDIDAT |
| Vtiger | Application | PHP | USER | À prioriser | CANDIDAT |
| YetiForce | Application | PHP | USER | À prioriser | CANDIDAT |
| OroCRM | Application | PHP | USER | À prioriser | CANDIDAT |
| EspoCRM | Application | PHP | USER | À prioriser | CANDIDAT |

## CRM et ERP applicatifs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Odoo | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Twenty | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |

## Facturation et finances

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Blesta | Application | PHP | USER | À prioriser | CANDIDAT |
| ClientExec | Application | PHP | USER | À prioriser | CANDIDAT |
| Invoice Ninja | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| InvoicePlane | Application | PHP | USER | À prioriser | CANDIDAT |
| WHMCS | Application | PHP | USER | À prioriser | CANDIDAT |
| Firefly III | Application | PHP | USER | À prioriser | CANDIDAT |
| Wallos | Application | PHP | USER | À prioriser | CANDIDAT |

## Projets et suivi des tâches

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Anuko Time Tracker | Application | PHP | USER | À prioriser | CANDIDAT |
| Kanboard | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| MantisBT | Application | PHP | USER | À prioriser | CANDIDAT |
| ProjeQtOr | Application | PHP | USER | À prioriser | CANDIDAT |
| Traq | Application | PHP | USER | À prioriser | CANDIDAT |
| Kimai | Application | PHP | USER | À prioriser | CANDIDAT |

## Projets collaboratifs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Planka | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Focalboard | Application | DEPLOY | MASQUE | À prioriser | REVUE RENFORCÉE |
| OpenProject | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Vikunja | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**OpenProject :** Recette applicative complète avec données persistantes et procédure de mise à jour.

Source : https://www.openproject.org/docs/installation-and-operations/installation/docker/

## Support et gestion IT

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| GLPI | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| FreeScout | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| HESK | Application | PHP | USER | À prioriser | CANDIDAT |
| osTicket | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Snipe-IT | Application | PHP | USER | À prioriser | CANDIDAT |

## Marketing et newsletters

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Mautic | Application | PHP | USER | À prioriser | CANDIDAT |
| Revive Adserver | Application | PHP | USER | À prioriser | CANDIDAT |
| phpList | Application | PHP | USER | À prioriser | CANDIDAT |

## Listes de diffusion Perl

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Dada Mail | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**Dada Mail :** Recette Perl/CGI et tâches d'envoi à qualifier ; ne pas traiter comme un paquet PHP-FPM générique.

## Fichiers et gestion documentaire

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Nextcloud | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| ownCloud Server | Application | PHP | USER | À prioriser | CANDIDAT |
| OpenDocMan | Application | PHP | USER | À prioriser | CANDIDAT |
| ProjectSend | Application | PHP | USER | À prioriser | CANDIDAT |
| SeedDMS | Application | PHP | USER | À prioriser | CANDIDAT |
| GateQuest File Manager | Application | PHP | MASQUE | À prioriser | REVUE RENFORCÉE |

## Stockage et partage applicatifs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Pydio | Application | QUALIFIER | MASQUE | À prioriser | À IDENTIFIER |
| AList | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| File Browser | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| FileCodeBox | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Obsidian LiveSync | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| SFTPGo | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Paperless-ngx | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| ownCloud Infinite Scale | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Pydio Cells | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**Pydio :** Nom sans édition : distinguer l'ancien produit PHP de Pydio Cells. Pas de recette autorisée avant identification.

## Galeries photographiques

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Piwigo | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Chevereto | Application | PHP | USER | À prioriser | CANDIDAT |
| Coppermine | Application | PHP | USER | À prioriser | CANDIDAT |
| Gallery | Application | QUALIFIER | MASQUE | À prioriser | À IDENTIFIER |
| iGalerie | Application | PHP | USER | À prioriser | CANDIDAT |
| Lychee | Application | PHP | USER | À prioriser | CANDIDAT |
| TinyWebGallery | Application | PHP | USER | À prioriser | CANDIDAT |
| Zenphoto | Application | PHP | USER | À prioriser | CANDIDAT |

**Gallery :** Identifier exactement le projet, la branche, sa maintenance et les versions supportées avant tout affichage public.

## Photographies, médias et bibliothèque

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| PhotoPrism | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Jellyfin | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Navidrome | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Komga | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Calibre-Web | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Audiobookshelf | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Immich | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |

## Publication de podcasts

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Podcast Generator | Application | PHP | USER | À prioriser | CANDIDAT |

## Statistiques, sondages et formulaires

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| LimeSurvey | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Advanced Poll | Application | PHP | MASQUE | À prioriser | REVUE RENFORCÉE |
| Matomo | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| Open Web Analytics | Application | PHP | USER | À prioriser | CANDIDAT |
| SEO Panel | Application | PHP | USER | À prioriser | CANDIDAT |
| Form Tools | Application | PHP | USER | À prioriser | CANDIDAT |

## Analyse de données et no-code

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Umami | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Metabase | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| NocoDB | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| NocoBase | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Teable | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Baserow | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Appsmith | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Apache Superset | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Favoris et raccourcis

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| SiteBar | Application | PHP | USER | À prioriser | CANDIDAT |
| wallabag | Application | PHP | USER | À prioriser | CANDIDAT |
| YOURLS | Application | PHP | USER | P0 — à qualifier | CANDIDAT |

## Lecture et agrégation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| FreshRSS | Application | PHP | USER | P0 — à qualifier | CANDIDAT |
| selfoss | Application | PHP | USER | À prioriser | CANDIDAT |
| Tiny Tiny RSS | Application | PHP | USER | À prioriser | CANDIDAT |

## Veille et flux applicatifs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Miniflux | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| linkding | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| RSSHub | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Changedetection.io | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| TrendRadar | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| NewsNow | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Utilitaires et tableaux de bord

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Stirling-PDF | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Memos | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Draw.io | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Excalidraw | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| IT-Tools | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| JSON Hero | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Dashy | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Homepage | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Glance | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| Heimdall | Application | DEPLOY | USER | À prioriser | CANDIDAT |
| ConvertX | Application | DEPLOY | USER | À prioriser | CANDIDAT |

## Bureautique collaborative

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| ONLYOFFICE Docs | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Collabora Online | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Penpot | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Utilitaire historique

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| iTron Clock | Application | QUALIFIER | MASQUE | À prioriser | REVUE RENFORCÉE |

## Automatisation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| n8n | Application | ISOLE | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Node-RED | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Huginn | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Arcadia | Application | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Activepieces | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**n8n :** Valider le modèle commercial et la licence. Choisir une recette explicite : base, task runners, queue éventuelle. Ne pas importer implicitement une sandbox privilégiée.

Source : https://docs.n8n.io/n8n-community-license/community-license/license-faq.md

**Arcadia :** Plateforme de scripts : aucun droit système WCP360 implicite ; déploiement cloisonné.

Source : https://arcadia.cool/docs/quick-start

## Notifications

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| ntfy | Service | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Gotify | Service | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Agents et applications IA

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| OpenClaw | Application IA | ISOLE | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| DeepSeek Harness | Application IA | LAB | LAB-PRIVE | Laboratoire | LABORATOIRE |
| QwenPaw | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Hermes Agent | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| ClawSwarm | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| UPage | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Octop | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| BettaFish | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Vane | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Langflow | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Open WebUI | Application IA | ISOLE | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| New API | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Sub2API | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| DBHub | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Laya Server | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Dify | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Flowise | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| AnythingLLM | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| LibreChat | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| LiteLLM | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Langfuse | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| MaxKB | Application IA | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |

**OpenClaw :** Une passerelle et des secrets par frontière de confiance. Pas de socket Docker hôte, ni privilèges WCP360. Audit de sécurité avant activation.

Source : https://docs.openclaw.ai/gateway/security

**DeepSeek Harness :** La documentation 1Panel consultée le présente en developer preview : proposition de laboratoire, sans données sensibles.

Source : https://1panel.pro/docs/v2/user_manual/appstore/deepseek-harness/

**QwenPaw :** Le catalogue 1Panel utilise le nom QwenPaw et le slug copaw. Vérifier le paquet exact et son éditeur.

Source : https://1panel.pro/apps/copaw

**Hermes Agent :** Agent à outils et exécution : instance isolée, secrets dédiés, permissions bornées.

Source : https://1panel.pro/apps/hermes-agent

**Octop :** Référence amont identifiée ; ne pas assimiler le multi-utilisateur applicatif à l'isolation entre clients WCP360.

Source : https://github.com/TencentCloud/Octop

**BettaFish :** Requiert la qualification des modèles, API externes et collectes. Ne pas promettre une analyse exacte ni une absence de coûts externes.

Source : https://1panel.pro/apps/bettafish

**Vane :** Moteur de réponses IA ; qualifier les fournisseurs et dépendances de recherche.

Source : https://1panel.pro/apps/vane

**Laya Server :** Recette distincte du modèle Laya et de l'API tierce Jev ; capacités et modèle à qualifier.

Source : https://github.com/1Panel-dev/laya-server

**Dify :** Ensemble multi-services, dépendances, sandbox et licence à qualifier avant exposition.

Source : https://docs.dify.ai/en/self-host/deploy/quick-start/docker-compose

## Moteurs et création IA

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Ollama | Service IA | CPU-GPU | ADMIN-DELEGABLE | P1 — à qualifier | CANDIDAT |
| vLLM | Service IA | CPU-GPU | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| LocalAI | Service IA | CPU-GPU | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| llama.cpp | Service IA | CPU-GPU | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| SGLang | Service IA | CPU-GPU | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| ComfyUI | Service IA | CPU-GPU | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Bases de données

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| MySQL | Service | SERVICE | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| MariaDB | Service | SERVICE | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| PostgreSQL | Service | SERVICE | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| MongoDB | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Microsoft SQL Server | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Redis | Service | SERVICE | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| Memcached | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| ClickHouse | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| InfluxDB | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| StarRocks | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Valkey | Service | SERVICE | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| Apache CouchDB | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Qdrant | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Chroma | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Weaviate | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Recherche et indexation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Elasticsearch | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Meilisearch | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Typesense | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Manticore Search | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| OpenSearch | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Extensions de bases

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| pgvector | Extension | EXTENSION | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| PostGIS | Extension | EXTENSION | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

**pgvector :** Extension PostgreSQL ; activation limitée à une base autorisée. Pas sur la base interne WCP360.

Source : https://github.com/pgvector/pgvector

**PostGIS :** Extension PostgreSQL ; matrice de versions et permissions à qualifier.

Source : https://postgis.net/documentation/

## Clusters et réplication

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| MySQL-Cluster | Recette multi-nœuds | CLUSTER | ADMIN | À prioriser | CANDIDAT |
| PostgreSQL-Cluster | Recette multi-nœuds | CLUSTER | ADMIN | À prioriser | CANDIDAT |
| Redis-Cluster | Recette multi-nœuds | CLUSTER | ADMIN | À prioriser | CANDIDAT |

**MySQL-Cluster :** Nom à désambiguïser : recette de réplication n'est pas nécessairement MySQL NDB Cluster. Tests de panne et restauration requis.

Source : https://1panel.pro/

**PostgreSQL-Cluster :** Recette multi-nœuds, pas une nouvelle base. Nœuds indépendants et tests de bascule obligatoires.

**Redis-Cluster :** Recette multi-nœuds ; ne pas confondre réplication, Sentinel et Redis Cluster.

## Administration des données

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| DBX | Outil administratif | ISOLE | USER-BORNE | À prioriser | CANDIDAT |
| phpMyAdmin | Outil administratif | ISOLE | USER-BORNE | Fondation | CANDIDAT |
| Adminer | Outil administratif | ISOLE | USER-BORNE | Fondation | CANDIDAT |
| pgAdmin 4 | Outil administratif | ISOLE | USER-BORNE | Fondation | CANDIDAT |
| mongo-express | Outil administratif | ISOLE | USER-BORNE | À prioriser | CANDIDAT |
| Redis Commander | Outil administratif | ISOLE | USER-BORNE | À prioriser | CANDIDAT |
| CloudBeaver | Outil administratif | ISOLE | USER-BORNE | À prioriser | CANDIDAT |

**DBX :** Identifier précisément l'édition web Docker et restreindre les bases autorisées.

Source : https://1panel.pro/apps/dbx

**phpMyAdmin :** Administration MySQL/MariaDB ; ce n'est pas un gestionnaire de formulaires. Accès aux bases du client uniquement.

Source : https://www.phpmyadmin.net/

## Stockage objet

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| RustFS | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Silo | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

**Silo :** Candidat retenu : PGSTY Silo, stockage objet. Identifier le dépôt, ne pas confondre avec des homonymes.

Source : https://github.com/pgsty/silo

## Messagerie et middleware

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| RabbitMQ | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Apache Kafka | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Apache RocketMQ | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| NATS | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| EMQX | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Consul | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| OpenLDAP | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Consoles de middleware

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Redpanda Console | Outil administratif | ISOLE | USER-BORNE | À prioriser | CANDIDAT |

## Serveurs web

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Nginx | Composant infrastructure | INFRA | ADMIN | Fondation | CANDIDAT |
| Apache HTTP Server | Composant infrastructure | INFRA | ADMIN | Fondation | CANDIDAT |
| Caddy | Composant infrastructure | INFRA | ADMIN | Fondation | CANDIDAT |
| OpenLiteSpeed | Composant infrastructure | INFRA | ADMIN | À prioriser | CANDIDAT |
| Apache Tomcat | Composant infrastructure | INFRA | ADMIN | À prioriser | CANDIDAT |
| Nginx Proxy Manager | Composant infrastructure | INFRA | ADMIN | À prioriser | CANDIDAT |
| OpenResty | Composant infrastructure | INFRA | ADMIN | À prioriser | CANDIDAT |

**OpenLiteSpeed :** Option d'infrastructure à évaluer ; ne change pas la proposition Nginx/Apache/Caddy de WCP360.

**Nginx Proxy Manager :** Ne doit pas concurrencer le frontal et les certificats gérés par WCP360 ; nœud séparé ou intégration explicitement validée.

## Messagerie électronique

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Maddy Mail Server | Service | INFRA | ADMIN | À prioriser | CANDIDAT |
| Stalwart Mail Server | Service | INFRA | ADMIN | À prioriser | CANDIDAT |
| Mailserver | Service | QUALIFIER | MASQUE | À prioriser | À IDENTIFIER |

**Mailserver :** Nom générique : identifier le dépôt et l'édition avant d'écrire une recette ; ne pas le confondre silencieusement avec Docker Mailserver.

## Webmail

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Roundcube | Application | PHP | USER | P0 — à qualifier | CANDIDAT |

## Sécurité et identités

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Bitwarden | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| 2FAuth | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Keycloak | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| ZITADEL | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Casdoor | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Logto | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| JumpServer | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Vaultwarden | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Authentik | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Réseau et accès distants

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| OpenVPN | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |
| AdGuard Home | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |
| cloudflared | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |
| NPS | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |
| NPC | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |
| RustDesk | Service sensible | INFRA | ADMIN | À prioriser | CANDIDAT |

## Observabilité et supervision

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Zabbix Server | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Zabbix Proxy | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Zabbix Agent | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Uptime Kuma | Service | SERVICE | ADMIN-DELEGABLE | P1 — à qualifier | CANDIDAT |
| Grafana | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Prometheus | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Node Exporter | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Beszel | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Netdata | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Gatus | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Jaeger | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Vector | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Logstash | Service | SERVICE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Git et collaboration technique

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Gitea | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Forgejo | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| GitLab | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| OneDev | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Opengist | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## CI/CD, registres et qualité

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Jenkins | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| SonarQube | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Nexus Repository | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Docker Registry | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Verdaccio | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Bytebase | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Harness | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Gitea Act Runner | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Forgejo Actions Runner | Service sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Environnements développeurs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| code-server | Outil sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Atheos | Outil sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Jupyter Notebook | Outil sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Browserless | Outil sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Web-Check | Outil sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Gestion de conteneurs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Portainer CE | Outil administratif | INFRA | ADMIN | À prioriser | CANDIDAT |
| Dockge | Outil administratif | INFRA | ADMIN | À prioriser | CANDIDAT |
| Watchtower | Outil administratif | INFRA | ADMIN | À prioriser | CANDIDAT |

**Portainer CE :** Ne pas exposer le moteur de conteneurs WCP360 aux clients via cette interface.

**Dockge :** Gestionnaire externe de stacks : éviter un second propriétaire des déploiements WCP360.

**Watchtower :** Désactivé par défaut pour les stacks gérées : les mises à jour doivent passer par la chaîne WCP360.

## Domotique et transfert

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Home Assistant | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| qBittorrent | Application sensible | ISOLE | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Jeux

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Palworld Server | Service sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |
| Palworld Server Tool | Service sensible | ISOLE | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Laboratoire sécurité

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| DVWA | Application de laboratoire | LAB | LAB-PRIVE | Laboratoire | LABORATOIRE |

**DVWA :** Application volontairement vulnérable ; laboratoire privé isolé, jamais un hébergement public de production.

Source : https://github.com/digininja/DVWA/blob/master/README.fr.md

## Virtualisation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Windows | Système invité | VM | ADMIN | À prioriser | CANDIDAT |

**Windows :** Recette de machine virtuelle séparée ; hyperviseur, droits de virtualisation et licence à valider. Pas un simple site ni un conteneur Linux ordinaire.

## Runtimes

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| PHP | Runtime | RUNTIME | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| PHP-FPM | Runtime | RUNTIME | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| FrankenPHP | Runtime | RUNTIME | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| Node.js | Runtime | RUNTIME | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| Python | Runtime | RUNTIME | ADMIN-DELEGABLE | Fondation | CANDIDAT |
| Java | Runtime | RUNTIME | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Go | Runtime | RUNTIME | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| .NET | Runtime | RUNTIME | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Ruby | Runtime | RUNTIME | ADMIN-DELEGABLE | À prioriser | CANDIDAT |
| Perl | Runtime | RUNTIME | ADMIN-DELEGABLE | À prioriser | CANDIDAT |

## Frameworks PHP

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Laravel | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | P0 — modèle | CANDIDAT |
| Symfony | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | P0 — modèle | CANDIDAT |
| CodeIgniter | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| CakePHP | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Nette | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Yii | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| FuelPHP | Modèle de projet | TEMPLATE | MASQUE | À prioriser | REVUE RENFORCÉE |
| Slim | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

## Bibliothèques PHP

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Smarty | Bibliothèque | DEPENDANCE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| SimplePie | Bibliothèque | DEPENDANCE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

**Smarty :** Moteur de templates PHP : dépendance d'un projet, pas une application autonome.

Source : https://www.smarty.net/

**SimplePie :** Bibliothèque d'analyse de flux RSS/Atom PHP, pas un lecteur autonome à déployer.

Source : https://simplepie.org/

## Frameworks et interfaces JavaScript

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Next.js | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| SvelteKit | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Nuxt | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Ember.js | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Gatsby | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Remix | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Svelte | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Blitz.js | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Preact | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Vue.js | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Angular | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Ionic | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Polymer | Modèle de projet | TEMPLATE | MASQUE | À prioriser | REVUE RENFORCÉE |
| Gridsome | Modèle de projet | TEMPLATE | MASQUE | À prioriser | REVUE RENFORCÉE |
| UmiJS | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Sapper | Modèle de projet | TEMPLATE | MASQUE | Hors nouveautés | MIGRATION SEULE |
| Stencil | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| RedwoodJS | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| React | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Astro | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

**Sapper :** Prédécesseur déprécié de SvelteKit : migration uniquement, pas de nouvelle installation recommandée.

Source : https://sapper.svelte.dev/docs/

## Générateurs de sites et documentation

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Hexo | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Eleventy | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Docusaurus | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Hugo | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Middleman | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Zola | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Material for MkDocs | Modèle de projet | BUILD-STATIQUE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

## Outils de build

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Vite | Outil de construction | BUILD | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Parcel | Outil de construction | BUILD | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

**Vite :** Outil de construction frontend ; déployer le résultat de production, pas le serveur de développement.

Source : https://vite.dev/guide/

**Parcel :** Outil de construction frontend ; pas une application de production autonome.

## CMS applicatifs

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Ghost | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Strapi | Application | DEPLOY | USER-CONDITIONNEL | P1 — à qualifier | CANDIDAT |
| Directus | Application | DEPLOY | USER-CONDITIONNEL | À prioriser | CANDIDAT |

## Extensions applicatives

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| WooCommerce | Extension | EXTENSION | USER | P0 — à qualifier | CANDIDAT |

**WooCommerce :** Extension WordPress : installer un ensemble WordPress + WooCommerce compatible ; ne pas créer une seconde application indépendante.

## Frameworks Python

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Django | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| FastAPI | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Flask | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

## Frameworks JavaScript serveur

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Express | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| NestJS | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Fastify | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

## Autres frameworks backend

| Référence | Nature | Profil | Accès proposé | Lot | Qualification |
|---|---|---|---|---|---|
| Ruby on Rails | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| Spring Boot | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |
| ASP.NET Core | Modèle de projet | TEMPLATE | USER-DEVELOPPEUR | À prioriser | CANDIDAT |

## Sources générales de repérage

https://installatron.com/appstable

https://1panel.pro/

https://coolify.io/docs/services/overview

Les liens ligne par ligne et l'origine exacte sont conservés dans le CSV et le classeur.