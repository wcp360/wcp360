# Reconstruction de System Settings — 1er novembre 2026

> État de la première reconstruction. La [restauration de la richesse fonctionnelle](28-system-settings-functional-restoration.md) décrit la version suivante : 24 rubriques et 248 réglages et listes.

[System Settings](system-settings.html) est reconstruit autour de **9 sections et 86 champs** : identité du serveur, réseau de gestion, accès et sessions, ressources par défaut, socle de sécurité, exploitation, identité et assistance, gouvernance et conservation, intégrations. L’historique constitue une vue séparée. La page propose une recherche globale des champs, leurs erreurs et modifications, des liens vers les centres responsables, ainsi qu’une navigation adaptée aux petits écrans.

Cette reconstruction remplace les anciens panneaux hétérogènes et les confirmations de réussite sans persistance. Les inventaires de services, bases, sauvegardes ou logiciels restent dans leurs centres spécialisés ; System Settings définit les règles générales et les points d’intégration. Toutes les opérations restent une démonstration locale, sans application au système d’exploitation.

## Architecture et persistance

| Fichier | Responsabilité |
| :--- | :--- |
| [settings-model.js](assets/settings-model.js) | Définition des sections et champs, valeurs initiales, migration des données, validation, comparaison et sérialisation |
| [settings-store.js](assets/settings-store.js) | Brouillons, révisions, instantanés, import/export, reprise après erreur et détection des modifications concurrentes |
| [system-settings.js](assets/system-settings.js) | Rendu, recherche, édition, erreurs, revue des changements et historique |
| [system-settings.css](assets/system-settings.css) | Présentation dédiée, états des champs, navigation et adaptation de la mise en page |
| [system-settings.html](system-settings.html) | Structure ROOT et chargement ordonné des dépendances |

La configuration appliquée utilise `wcp360-system-settings-v1`. Le brouillon utilise une clé distincte, `wcp360-system-settings-draft-v2`. Ouvrir la page ou modifier un champ ne remplace donc pas la configuration appliquée. La première ouverture affiche une configuration de référence, pas une révision déjà enregistrée.

La validation porte notamment sur les contacts, domaines, adresses IP/CIDR, ports, fuseaux horaires, limites numériques et dépendances entre règles. Les données métier historiques et les métadonnées reconnues sont conservées lors des migrations et réparations ; la sérialisation écarte les secrets littéraux et identifiants présents dans les URL.

## Parcours de modification

1. Modifier les champs crée un brouillon local. Les champs modifiés peuvent être rétablis individuellement ; le brouillon complet peut être abandonné.
2. Ouvrir la revue affiche les valeurs avant/après. La publication demande un motif et, pour un changement à risque élevé ou une réparation, une confirmation explicite.
3. La publication vérifie les valeurs et l’absence de changement externe, puis enregistre une seule nouvelle révision avec son historique. Une erreur de stockage conserve le brouillon et ne produit pas de faux succès.
4. L’historique conserve jusqu’à 12 instantanés. Restaurer un instantané ou importer un JSON prépare un brouillon à revoir : ces actions ne publient pas directement.

Une modification externe peut être rechargée ou servir de nouvelle base au brouillon ; les champs en collision sont signalés. Les brouillons issus d’une autre base et les données illisibles demandent une reprise explicite. L’export distingue configuration appliquée et brouillon. Une éventuelle tâche affichée par le moteur du prototype reste simulée.

Un champ numérique laissé vide conserve cette valeur dans le brouillon, y compris après rechargement ou reprise d’une ancienne base : l’erreur reste visible au lieu d’être remplacée silencieusement par une valeur initiale. Un brouillon enregistré malformé ou tronqué est signalé et peut être supprimé après confirmation. Les instantanés de brouillon au format v2 doivent contenir tous les champs attendus. Si cette suppression échoue, l’erreur reste affichée et l’action peut être réessayée.

## Corrections complémentaires

Le [wizard initial](setup-wizard.html) enregistre maintenant les valeurs effectivement saisies pour le nom d’hôte, le fuseau, les contacts et l’IPv4, tout en préservant une configuration IPv6 existante. Les suites couvrent également l’échec de persistance.

Les quatre pages utilisant [root-control.js](assets/root-control.js) disposent de panneaux distincts, de la bonne cible de ligne et de formulaires dont les valeurs sont conservées dans des brouillons de démonstration. Les trois pages utilisant [governance-centers.js](assets/governance-centers.js) récupèrent les collections malformées, valident les valeurs et doublons, et annulent les mutations qui ne peuvent pas être enregistrées. Les détails et les pages concernées figurent dans l’[inventaire page par page](26-page-verification-inventory.md).

Huit liens de System Settings pointaient vers des espaces USER pour des réglages ROOT. Ils utilisent désormais les destinations ROOT du registre canonique ; un contrôle ciblé vérifie les liens et leur portail déclaré.

Trois centres ROOT supplémentaires ont été corrigés :

- Le [catalogue d’applications](application-catalog.html) garde les nouvelles applications et sources désactivées dans l’attente d’une revue locale. Les sources exigent une URL HTTPS ; une activation demande un motif et une confirmation. Les paramètres de déploiement saisis sont conservés.
- Le [gestionnaire de logiciels](software-manager.html) conserve et restitue les valeurs de la politique de maintenance : durée d’observation, conservation, plage et gel. Les demandes de paquet gardent la version cible et le périmètre sélectionné.
- Le [centre mail ROOT](admin-mail.html) utilise des formulaires distincts pour le profil choisi, l’ajout de nœud et les politiques. Les saisies restent associées à leur cible ; la recherche avancée filtre réellement les lignes de la file de démonstration.

Ces centres conservent les formulaires en cas d’échec d’écriture, annulent les mutations non enregistrées et ne créent pas de tâche fantôme. Leurs opérations, revues et inventaires restent locaux et fictifs. Les balises `main` imbriquées ont également été supprimées du catalogue, du centre DNS et du centre de transfert ; cette correction structurelle n’étend pas la preuve fonctionnelle aux deux derniers centres.

## Vérification et portée réelle

Dernière exécution : **25 suites, aucune en échec** ; vérification HTTP des **232 pages, 254 contrôles réussis**.

| Suite ciblée | Résultat | Portée |
| :--- | ---: | :--- |
| [Modèle des paramètres](tests/audit_wcp360_settings_model.mjs) | 138 contrôles | Valeurs, migrations, validations, comparaisons et portails des liens |
| [Stockage des paramètres](tests/audit_wcp360_settings_store.mjs) | 42 scénarios | Brouillons, publication, historique, erreurs et concurrence |
| [Contrôleur des paramètres](tests/audit_wcp360_settings_controller.mjs) | 20 scénarios | Véritable contrôleur avec DOM et stockage adaptés |
| [Wizard](tests/audit_wcp360_setup.mjs) | 57 contrôles | Configuration initiale et persistance |
| [Contrôle ROOT](tests/audit_wcp360_root_control.mjs) | 10 contrôles | Quatre pages et leurs brouillons ciblés |
| [Gouvernance](tests/audit_wcp360_governance.mjs) | 13 contrôles | Trois pages et leurs mutations |
| [Centres ROOT](tests/audit_wcp360_root_centers.mjs) | 23 scénarios | Catalogue, logiciels et mail ROOT avec contrôleurs réels adaptés |

L’[inventaire détaillé](26-page-verification-inventory.md), également disponible en [CSV](26-page-verification.csv), analyse le HTML initial des 232 pages : 64 redirections, 92 maquettes statiques ou shells, 59 centres avec contrôleur, 13 fiches ou opérations et 4 entrées. **32 pages ont une preuve de contrôleur ciblé par adaptateur ; 200 n’en ont pas dans les suites recensées.** Une dépendance partagée testée ou une réponse HTTP ne vaut pas vérification fonctionnelle de chaque page.

Le rendu initial de System Settings a été observé dans Chrome sur ordinateur, à 1069 × 822 et 1536 × 816, avant les derniers ajustements de présentation. Les interactions natives et le contrôle mobile n’ont pas été achevés. Le test optionnel `runtime_audit_wcp360.mjs` a été adapté au nouveau contrat et sa syntaxe vérifiée, mais il n’a pas été exécuté. Les adaptateurs ne certifient ni validation native, ni focus réel, ni lecteur d’écran, ni contrastes rendus, ni conformité WCAG. Aucune vérification complète des 232 parcours dans un navigateur n’est revendiquée.

Pour reproduire les contrôles depuis le dossier du prototype :

```sh
node tests/run.mjs
node tests/audit_wcp360_page_coverage.mjs --write
# Après démarrage du serveur de prévisualisation :
node tests/http_preview.mjs 8790
```

Les instructions détaillées figurent dans [tests/README.md](tests/README.md). Le [rapport 25](25-ui-ux-remediation.md) conserve la trace de la première passe ; ce document et l’inventaire 26 précisent les changements suivants et leurs limites.
