# Paramètres système — richesse fonctionnelle rétablie

La reconstruction précédente avait réduit System Settings à 9 rubriques et 86 champs. Cette version porte le centre à **24 rubriques, 248 réglages et listes**, avec une vue d’ensemble et un historique séparés. Les 248 éléments correspondent à 245 réglages simples et 3 collections configurables ; les colonnes et entrées de ces collections ne gonflent pas ce total.

Les domaines avancés disposent de formulaires dans [System Settings](system-settings.html). Les liens vers les centres spécialisés donnent accès à leurs inventaires et opérations ; ils ne remplacent plus ces paramètres.

## Organisation

| Famille | Rubriques |
| :--- | :--- |
| Socle de la plateforme | Identité du serveur, réseau, accès, ressources, sécurité, gouvernance |
| Services d’hébergement | Moteurs applicatifs, bases de données, messagerie, sauvegardes |
| Infrastructure | Stockage, IPAM, noyau et Livepatch, Edge/cache/CDN |
| Connectivité et signaux | Fournisseurs, intégrations, observabilité |
| Protection avancée | Zero Trust, forensics et preuves |
| Exploitation et optimisation | Exploitation, AIOps, énergie et sobriété |
| Identité visuelle | Identité et assistance, marque blanche |

La vue d’ensemble présente les 24 domaines et des aperçus du brouillon. La navigation regroupe les rubriques par famille ; le sélecteur mobile et la recherche globale donnent accès aux mêmes réglages. Les anciens fragments comme `#providers`, `#runtimes`, `#mail`, `#backups` ou `#zerotrust` ouvrent maintenant leur rubrique dédiée.

## Listes éditables

Les **connecteurs**, **destinations de stockage** et **pools IP** permettent l’ajout, l’édition et le retrait confirmé d’entrées. Les formulaires contrôlent les champs, références de clé, endpoints, identifiants et doublons. IPAM vérifie les CIDR, les chevauchements IPv4/IPv6 et l’appartenance de la passerelle au réseau. Une destination de sauvegarde référencée doit exister et être activée.

Ces opérations préparent le brouillon. Le comparatif montre les véritables enregistrements avant/après ; leur publication demande un motif et une confirmation pour les domaines sensibles. Une erreur de stockage reste visible et ne crée pas de tâche de réussite.

Les paramètres avancés incluent notamment les limites des langages, connexions de bases de données, transport et filtrage mail, conservation et tests de restauration, seuils de stockage, mTLS et identités de service, conservation des preuves, cache Edge, limites AIOps et personnalisation des partenaires. Ce sont des configurations de démonstration : aucun fournisseur, modèle distant, agent ou service système n’est contacté.

## Compatibilité des données

Le schéma passe à la version 3 ; les clés de configuration active et de brouillon restent inchangées. Un ancien brouillon v2 complet conserve ses 86 valeurs, y compris un nombre temporairement vide, et reçoit les nouveaux réglages par migration additive. Un brouillon tronqué reste refusé avec une action explicite de suppression.

Les valeurs historiques d’exécution, d’envoi PHP et de conservation des sauvegardes sont reprises. Lorsqu’une ancienne clé existe, sa valeur reste synchronisée avec le réglage correspondant. Les connecteurs de l’assistant conservent leur usage Mail/Stockage et leurs endpoints, y compris le format mail historique `hôte:port`. Les métadonnées et références protégées restent conservées ; les secrets littéraux sont retirés des exports.

## Vérification

Exécution complète : **25 suites réussies, aucun échec**. Vérification HTTP : **232 pages, 254 contrôles réussis**.

| Suite ciblée | Résultat |
| :--- | ---: |
| [Modèle](tests/audit_wcp360_settings_model.mjs) | 263 contrôles |
| [Stockage et migrations](tests/audit_wcp360_settings_store.mjs) | 46 scénarios |
| [Contrôleur](tests/audit_wcp360_settings_controller.mjs) | 27 scénarios |

Les scénarios exécutent les véritables modèle, stockage et contrôleur avec des adaptateurs DOM. Ils couvrent les 24 rubriques, les 248 réglages, la recherche, les collections, les erreurs, la revue et la persistance. La page chargée dans Chrome a également exposé les 24 rubriques et leurs aperçus dans l’arbre d’accessibilité. La capture visuelle n’a pas permis de conclure sur le rendu final ; la revue native des interactions et du mobile reste à compléter. L’audit navigateur optionnel a été adapté et sa syntaxe contrôlée, sans être exécuté.

L’[inventaire page par page](26-page-verification-inventory.md) conserve ses limites : 32 pages avec preuve de contrôleur ciblée et 200 sans cette preuve. Cette extension de System Settings ne certifie pas tous les parcours du prototype ni une conformité WCAG. Le [rapport 27](27-system-settings-reconstruction.md) conserve les résultats de la reconstruction précédente.

```sh
node tests/run.mjs
# Après démarrage de la prévisualisation :
node tests/http_preview.mjs 8790
```
