# Corrections UI, UX et fonctionnement — 1er novembre 2026

Cette passe porte sur le prototype HTML local WCP360 : composants partagés, navigation, formulaires, connexion de démonstration, états des opérations et intégrité des 232 pages. La marque, les centres fonctionnels canoniques et les 64 redirections de compatibilité sont conservés.

## Corrections appliquées

| Domaine | Défaut constaté | Correction |
| :--- | :--- | :--- |
| Connexion | Saisies envoyées par les anciens formulaires GET | Contrôleur de démonstration explicite ; champs exclus de la sérialisation ; anciens paramètres sensibles retirés de l’URL |
| Portails | Parcours de connexion incomplet pour RESELLER | Choix USER, RESELLER et ROOT, conservé pendant les deux étapes même si le stockage local est indisponible |
| MFA | Récupération sans effet | Code fictif à six chiffres et mode de récupération avec validation et messages accessibles |
| Structure | Panneaux de travail clonés dans les pages de connexion | Pages de connexion dédiées, feuille de style dédiée et suppression des anciens styles d’authentification du runtime des opérations |
| Initialisation | Sept pages chargeaient le shell avant sa dépendance `WCPUI` | Chargement de Foundation rétabli avant le shell ; ordre des dépendances contrôlé automatiquement |
| HTML | Absence de région principale dans six redirections mail et fragment SSL introuvable | Une région principale par page ; ancre SSL corrigée ; vérification des fragments HTML et des onglets pilotés par contrôleur |
| Formulaires | Soumissions asynchrones concurrentes | Une opération à la fois ; état `aria-busy` ; boutons et validation restaurés après réussite, erreur ou détachement |
| Grilles | Confirmations groupées répétées et configuration sans colonne visible | Verrou pendant la confirmation ; repli utilisable ; actions indisponibles désactivées |
| Tâches | Progression à 100 % pouvant rester en cours | Normalisation et transition de fin cohérentes ; déduplication des identifiants et récupération des états invalides |
| Recherche des tâches | Accents et échecs mal pris en charge | Recherche normalisée et filtre « Failed » disponible dans la page et le panneau |
| Rafraîchissements | Focus perdu lors du remplacement des listes | Restauration dans le même conteneur et conservation du défilement |
| Panneaux | Superposition des commandes, opérations et panneaux de contrôle | Événement partagé d’ouverture ; fermeture du panneau précédent ; restauration du fond et du déclencheur |
| Tiroirs métier | Gestion du clavier et de la fermeture recopiée dans les centres | 22 panneaux dans 20 contrôleurs utilisent `WCPUI.Overlay`, avec gestion commune du fond, des dialogues natifs, des noms accessibles et du focus |
| Clavier | Focus possible dans des panneaux fermés ; retours de focus imprévisibles | Panneaux fermés `inert`, parcours Tab borné, fermeture avec Échap et retour à un contrôle disponible |
| Navigation mobile | États ARIA divergents et menu conservant sa largeur réduite | Synchronisation des déclencheurs, identifiant contrôlé, gestion du redimensionnement et menu complet sur mobile |
| Recherche latérale | Raccourci `/` capturé pendant la saisie ou dans un dialogue | Respect des champs éditables et des dialogues ; ouverture de la navigation avant le focus |
| Noms accessibles | Icônes annoncées comme du texte et boutons sans nom sur mobile | Icônes décoratives masquées aux lecteurs d’écran ; libellés explicites pour les contrôles partagés |
| Données locales | Historique ou favoris malformés pouvant interrompre l’interface | Validation des enregistrements, résolution canonique des routes, contrôle du portail et déduplication |
| Contrastes | Texte secondaire et liens trop pâles en thème clair | Couleurs de texte distinctes des couleurs décoratives, palette adaptée au thème sombre et états d’erreur harmonisés |
| Styles | Variables CSS utilisées mais absentes | Définition des alias de texte, ombre et fonds sémantiques partagés |
| Responsive | Informations masquées, panneaux dépassant le viewport, petites cibles | Métriques conservées, défilement borné, unités de viewport dynamiques, typographie et cibles tactiles agrandies |
| Documentation | Commandes de lancement incompatibles avec le dossier courant | Exemples exécutables depuis `WCP360_Prototype` et identifiants fictifs documentés |

Les états de démonstration et la revue des opérations indiquent explicitement que les contrôles et les tâches sont simulés. Aucun serveur, compte réel ou service d’infrastructure n’est modifié par ces parcours.

## Vérification

Les suites statiques et les adaptateurs exécutent les vrais modèles et contrôleurs avec des frontières DOM et stockage simulées. Les nouveaux tests couvrent la connexion et les fragments, les soumissions concurrentes, les états des tâches et les panneaux partagés.

| Vérification | Résultat |
| :--- | :--- |
| Ensemble statique et contrôleurs | 18 suites, aucune erreur |
| Audit HTML existant | 232 pages, aucun résultat d’erreur ou d’avertissement |
| Connexion et intégrité des routes | 3 238 assertions, dont 139 fragments internes et 207 consommateurs de Foundation |
| Intégration | 62 scripts, 2 733 références locales, 4 021 assertions ; 64 redirections conservées |
| Régressions formulaires et tâches | 10 cas |
| Régressions commandes, navigation et panneaux | 20 cas |
| Cycle de vie modal partagé | 10 cas : panneaux imbriqués, isolation du fond, clavier, confirmations natives, destruction et remontage |
| Styles | 33 feuilles vérifiées ; aucune variable WCP360 référencée sans définition, aucune accolade déséquilibrée |
| Serveur local Node | 254 contrôles HTTP réussis sur 232 pages |

Les contrastes des couleurs partagées ont été calculés à partir de leurs valeurs : texte secondaire clair sur surface **4,80:1**, liens bleus sur fond clair **4,92:1**, texte blanc des boutons principaux **5,23:1**. Ces calculs ne constituent pas une mesure de chaque combinaison réellement rendue.

## Limites de validation

La revue visuelle et native du navigateur n’a pas abouti. L’hôte d’accès aux onglets était indisponible ; le contrôle Windows s’est ensuite arrêté parce qu’il ne pouvait pas déterminer l’URL active avec assez de certitude. Aucun autre contrôle de l’interface n’a été tenté après cet arrêt. Un audit Chrome isolé n’a pas fourni de résultat exploitable ; ses profils temporaires ont été nettoyés.

Les tests de rendu, de validation native, de zoom, de lecteur d’écran et de navigation tactile restent donc à effectuer dans un navigateur fonctionnel. Les adaptateurs testent les décisions des contrôleurs, sans certifier le rendu ni une conformité WCAG ou RGAA complète.

Les opérations d’hébergement restent des simulations locales. Cette passe ne fournit pas une authentification de production, une autorisation serveur, un envoi SMTP, une émission ACME ou une sauvegarde réelle.

## Reproduire les contrôles

Depuis le dossier du prototype, avec Node.js :

```sh
node tests/run.mjs
node serve-prototype.mjs
# Dans un deuxième terminal :
node tests/http_preview.mjs 8790
```

Le lancement sans installation supplémentaire sous Windows est documenté dans [README.md](README.md). Le détail des suites, y compris les audits navigateur distincts, se trouve dans [tests/README.md](tests/README.md).
