# WCP360 — Archive du 30 octobre 2026 : inventaire et plan d’intégration

Statut : PROPOSITION NON NORMATIVE — import des contenus non effectué.
Cette PR conserve uniquement ce rapport et l’inventaire, pas les fichiers de l’archive.
Aucune promotion de baseline, fusion, publication publique ou validation de production.

## Sources et méthode

Archive fournie par l’utilisateur : Web Control Panel 360.zip.
SHA-256 : 46d18fabfd23e749d58bbb736268338092bf2b9775bc7b3a4ebeaecad9ecbdeb.
Taille ZIP : 47 001 585 octets ; fichiers décompressés : 53 078 536 octets.
2 612 entrées ZIP : 2 586 fichiers et 26 entrées de répertoire.
Extraction contrôlée pour les chemins sortants et les liens symboliques.

Le dépôt yyogas/wcp360 a été vérifié privé.
Comparaison à main : 038617047fb6dc701ad1c2345f4742b890c745e2.
Code candidat séparé : PR #110, tête 31fd3fe4d9203d9e5dbc2b43843c6c9ec74f2940,
empilée sur #108. Ne pas remplacer SOURCE/daemon ni fusionner ces PR par cet import.

Chaque fichier a été lu en octets et empreinté (SHA-256 et Git blob).
2 208 fichiers texte, total 9 877 579 octets, ont fait l’objet d’une lecture
automatisée pour inventaire et recherche ciblée. Lecture approfondie des points
d’entrée, règles de design, documents de séparation core/extensions/INTERNAL,
manifestes, configuration PHP et code d’authentification de démonstration.
Ce travail n’est PAS une revue sémantique ligne par ligne des 1 819 Markdown,
ni un audit sécurité exhaustif ni une validation de chaque média ou licence.

## Inventaire vérifié

| Zone | Fichiers | Interprétation |
|---|---:|---|
| WCP360_Design_System | 309 | 232 HTML ; prototype UI, CSS/JS et règles locales |
| WCP360_Developper_docs | 1 803 | Corpus documentaire, snapshots et historique |
| WCP360_Official_Website | 443 | 15 HTML, 6 PHP, nombreux médias hérités |
| WCP360_Internal_controle | 9 | Prototype privé fournisseur, pas backend sécurisé |
| WCP360_Extensions | 8 | Connecteur Insights de démonstration |
| tools | 12 | Audits, résultats anciens et ZIP de sauvegarde |
| Racine | 2 | README et audit historique |
| WCP360_Source_Code | 0 | Répertoire réservé ; aucun code produit |
| WCP360_Documentation | 0 | Répertoire réservé |

41 fichiers sont strictement identiques à des blobs de main, tous dans
WCP360_Developper_docs. 155 groupes de doublons exacts dans l’archive,
soit 155 copies supplémentaires. Ne pas supprimer ces chemins automatiquement :
ils peuvent matérialiser des snapshots et être la cible de liens.

L’inventaire JSON associé conserve chemin, taille, SHA-256 et Git blob pour
les 2 586 fichiers. Les deux répertoires vides réservés doivent être décrits par
l’index d’import : Git ne versionne pas un répertoire vide.

## Contrôles exécutés

- Script fourni tools/audit_wcp360_design_system.mjs, après lecture :
  232 pages ; aucun constat selon les règles limitées de ce script.
- node --check : 55 fichiers JavaScript/MJS, zéro erreur de syntaxe.
- Recherche ciblée de marqueurs de clés privées, tokens GitHub et clés AWS :
  aucun marqueur correspondant trouvé. Ce résultat ne garantit pas l’absence
  d’autres secrets, données personnelles ou contenus confidentiels.
- Audits navigateur/runtime NON exécutés ; tests PHP NON exécutés.
- Les anciens JSON de résultats ne sont pas des preuves de cette exécution.
- Les tests statiques ne prouvent ni WCAG, ni isolation serveur, ni MFA réel,
  ni fonctionnement du pipeline de publication.

## Divergences à résoudre avant promotion

| Référence | Constat et preuve | Traitement / propriétaire |
|---|---|---|
| IMPORT-01 | STYLE_LOCK et README du design déclarent V2.5.3.12 ; référence projet V2.5.3.36.4 | Design + MASTER : comparer le delta, conserver le statut candidat |
| IMPORT-02 | PROJECT_AUDIT du 27 septembre décrit des anciens dossiers et des zones alors vides | Garder historique, ajouter un index actuel ; ne pas réécrire la preuve originale |
| IMPORT-03 | Manifeste privé, README et scripts runtime citent WCP360 Internal / WCP360 Extensions / WCP360 Design System, différents des noms présents | Corriger seulement dans une copie de travail, journaliser chaque changement |
| IMPORT-04 | Site officiel : configuration et formulaires PHP NETTOYAGE 360, logos/photos/CSS hérités | Séparer historique et site WCP360 ; ne pas déployer ces endpoints comme API WCP360 |
| IMPORT-05 | internal-auth.js crée la session dans sessionStorage après contrôle du suffixe e-mail | Simulation UI uniquement : aucune preuve SSO/FIDO2 ; implémentation serveur distincte requise |
| IMPORT-06 | Corpus ancien annonce NO-GO / CODE NONE ; main et branches ont leur propre historique | Ne pas importer ces statuts comme décisions actuelles ; liens vers preuves GitHub |
| IMPORT-07 | Docs 08/09 parlent de décisions d’architecture et contiennent des exemples de versions/commits | Conserver comme propositions issues de l’archive jusqu’à revue des autorités |
| IMPORT-08 | Doc 08 classe DNS/mail/DB/backup comme core ; doc 09 présente aussi ces familles comme modules sélectionnés par édition | Modules + API + Security + MASTER : clarifier les garanties et les éditions avant implémentation |
| IMPORT-09 | Des audits runtime utilisent un chemin Chrome Windows et des anciens noms de dossiers | Adapter la configuration et rejouer avant toute affirmation de PASS navigateur |

Ne pas réintroduire le Sprint 0 annulé. Aucun nouveau contrat canonique ne peut
être déduit du seul titre « decision » dans l’archive.

## Organisation proposée

Le dépôt existant reste privé et conserve ses chemins et références actuels.

| Source | Conservation fidèle proposée | Destination de travail proposée |
|---|---|---|
| Documentation développeur | archive/imports/2026-10-30/developer-docs/ | documentation/reference-candidates/workspace-2026-10-30/ |
| Design System | archive/imports/2026-10-30/design-system/ | prototypes/control-panel/ |
| Site officiel | Archive de réception privée, incluant les éléments historiques | prototypes/official-website/ après tri des dépendances |
| INTERNAL | Dépôt distinct wcp360-internal, privé | prototypes/vendor-control-plane/ |
| Connecteur Insights | Dépôt distinct wcp360-insights-connector, privé au départ | prototypes/console/ et manifestes candidats |
| Outils | Archive de réception avec chemins d’origine | tools/prototype-audits/ après adaptation |
| README / audit historique | archive/imports/2026-10-30/ | Index documentaire actuel distinct |

Ces chemins sont proposés, pas encore créés. Le statut « public » d’un composant
désigne sa distribution future ; il ne demande PAS de rendre le dépôt GitHub public.

Pour conserver l’intégralité des octets sans multiplier les copies actives,
prévoir une archive de réception immuable dans un dépôt d’archives privé dédié
ou un stockage d’artefacts privé avec son empreinte. Ne pas déposer le ZIP complet
dans un dépôt destiné à devenir public : son historique contiendrait INTERNAL.
Les contenus hérités NETTOYAGE 360 et le ZIP de sauvegarde interne y restent
historiques, sans entrer dans les builds WCP360.

Le choix des nouveaux dépôts et leur politique d’accès reste à formaliser.
Ne pas créer une architecture de production par simple déplacement de prototypes.

## Ordre concret d’intégration

1. Conserver l’archive originale et son manifeste d’empreintes. Identifier les
   contenus confidentiels/tiers et leurs droits avant copie dans chaque dépôt.
2. Ouvrir une branche d’import isolée depuis une tête vérifiée. Importer le
   corpus comme référence candidate, avec provenance et mapping source/cible.
3. Importer le prototype UI sans le présenter comme version canonique ; conserver
   d’abord ses chemins internes, puis corriger les références dans un commit séparé.
4. Isoler les fichiers du site officiel effectivement utilisés. Les dépendances
   CSS/JS/images doivent être parcourues avant d’archiver les ressources héritées :
   l’absence d’un lien dans la navigation ne prouve pas l’absence d’usage.
5. Conserver INTERNAL dans son dépôt privé séparé ; Insights dans son cycle
   indépendant. Aucun lien d’accès ou secret privé dans les artefacts publics.
6. Rejouer les contrôles statiques, syntaxe, liens HTML/CSS/JS/Markdown, puis les
   parcours navigateur, responsive et accessibilité. Ne pas réutiliser les anciens PASS.
7. Faire les handoffs ciblés ci-dessous ; promouvoir seulement les deltas validés.
   Les spécifications approuvées sous specifications/ restent inchangées jusque-là.
8. Comparer l’inventaire final aux empreintes attendues : aucun fichier perdu,
   chaque modification justifiée, aucune promotion implicite.
9. Fusionner seulement après les revues applicables. Aucune release dans cet import.

## WCP360 HANDOFF / CONFLICT à traiter

Ce tableau documente le routage requis ; aucun message aux autres discussions,
aucune acceptation propriétaire ni revue indépendante n’a été effectué ici.

- MASTER : provenance, statut candidat, import non destructif et organisation des dépôts.
- Design System : IMPORT-01, comparaison ciblée des écrans et règles visuelles.
- Documentation : normalisation ciblée du delta uniquement ; aucun ordre de
  rescanner le ZIP ou de recommencer l’audit des 1 803 fichiers.
- Modules / API / Security : IMPORT-07/08, core minimal et droits des éditions.
- Security : séparation fournisseur/client, identité INTERNAL, consentement,
  absence de commande privilégiée générique, revue des contenus avant exposition.
- DevOps : import reproductible, archives privées et futurs builds par liste
  positive de composants ; travailleurs publics sans accès aux dépôts privés.
- QA : validation des prototypes et distinction des preuves statiques/runtime.

## Limites de cette PR

Cette PR ajoute uniquement le plan et le manifeste. Les 2 586 fichiers ne sont
PAS encore publiés sur GitHub. Aucun code applicatif n’a été modifié, aucun dépôt
rendu public, aucun prototype déclaré prêt pour production. Les schémas, moteurs,
frameworks, migrations et signatures ne sont pas décidés par ce rapport.
