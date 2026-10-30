# WCP360 — Import fidèle du workspace, lot 1

Statut : ARCHIVE DE RÉFÉRENCE CANDIDATE — PAS UNE BASELINE ACTIVE.

Source : Web Control Panel 360.zip, fournie par l’utilisateur.
SHA-256 archive : 46d18fabfd23e749d58bbb736268338092bf2b9775bc7b3a4ebeaecad9ecbdeb.
Date de réception/inventaire : 2026-10-30.
Plan d’intégration : [PR #111](https://github.com/yyogas/wcp360/pull/111).

## Contenu réellement importé

- workspace/WCP360_Developper_docs/ : 1 803 fichiers, 3 361 869 octets.
- workspace/WCP360_Design_System/ : 309 fichiers, 4 286 203 octets.
- Total source : 2 112 fichiers, 7 648 072 octets.

Les fichiers sont conservés octet pour octet : pas de normalisation de lignes,
réécriture, renommage interne ni suppression de doublons. Le favicon binaire
est également conservé. INVENTORY.json associe chaque chemin d’origine au chemin
Git et contient taille, empreinte SHA-256 et identifiant Git blob attendu.

Les répertoires vides WCP360_Source_Code et WCP360_Documentation n’apportent
aucun contenu. Le code Go candidat des PR #108/#110 reste indépendant.

## Autorité documentaire et limites

Les mentions « APPROVED », « canonical », « decision », NO-GO, les numéros de
versions et les anciens résultats de tests contenus dans ce snapshot sont des
déclarations de leur source historique, pas de nouvelles décisions du dépôt.
Aucun fichier sous specifications/, governance/ ou SOURCE/ n’est remplacé.
DOC v4.0.21.23 et DESIGN V2.5.3.36.4 ne sont pas modifiés par cet import.
Le Sprint 0 annulé n’est pas réactivé.

Le prototype utilise du stockage navigateur et des simulations. Ce n’est pas
un panneau d’hébergement déployable ni une preuve de sécurité serveur.
Le STYLE_LOCK de ce lot déclare V2.5.3.12 : divergence à traiter selon la PR #111,
pas à corriger silencieusement dans la copie fidèle.

Les liens vers les autres parties du workspace, ou vers les anciens noms de
dossiers, peuvent être absents/cassés dans cet import partiel. Ils sont préservés
comme preuve. Les corrections doivent être réalisées dans une copie de travail
ou un commit distinct, avec comparaison des empreintes et tests de liens.

## Éléments encore non importés

474 fichiers restent hors de ce lot : site officiel (443), contrôle interne
privé (9), extension Insights (8), outils (12), README et audit racine (2).
Les services INTERNAL doivent rester séparés du produit distribuable ; aucun
changement de visibilité ni dépôt public n’est créé ici.
Le site contient des fichiers historiques NETTOYAGE 360 à trier avant adaptation.

## Validation

L’inventaire de source et les Git blobs servent à vérifier l’exhaustivité et
l’identité de tous les fichiers après création de l’arbre Git distant.
Lors de l’examen initial : 232 pages passent le script statique fourni ;
55 JS/MJS de l’ensemble de l’archive passent node --check.
Ces vérifications ne sont ni des tests navigateur, ni un audit complet.
Aucun script du snapshot n’est installé comme workflow GitHub Actions.
Aucun build public, déploiement ou promotion de baseline n’est effectué.

Ne jamais publier tout cet arbre comme un ZIP produit : une release nécessite
une liste positive de composants approuvés et des contrôles indépendants.
