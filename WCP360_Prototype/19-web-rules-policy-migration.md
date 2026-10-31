# Règles web, politique ROOT et liens SSL — 30 octobre 2026

Cette passe remplace les deux présentations statiques Web Compatibility et Web Routing par des parcours persistants. Les grilles, formulaires, confirmations et jobs restent ceux de Foundation et `operations-core.js`. Le shell et le langage visuel de V2.5.3.12 sont conservés.

## Propriétaires et composants

| Centre | Propriétaire | Contenu de cette passe |
| --- | --- | --- |
| `web-compatibility.html` | USER / Operations / Web Compatibility | Brouillon par site, redirections exactes, MIME, contenu/HTTPS, validation, publications et versions conservées |
| `web-routing.html` | ROOT / Web & Data / Web Routing & Domains | Brouillon et publication des contraintes appliquées aux nouvelles publications USER |
| `ssl.html` | USER / SSL / TLS | Certificats simulés et versions liées aux hostnames, source commune de disponibilité HTTPS |
| `websites.html` | USER / Websites | Inventaire et liens vers les centres qui possèdent les réglages |

Les deux nouveaux fichiers sont `assets/web-rules-schemas.js` et `assets/web-rules-center.js`. Les modules historiques `web-compatibility` et `web-routing-governance` ont été retirés de `hosting-parity.js`. Aucun nouveau doublon de page ou de produit n’est ajouté dans la sidebar.

Les sous-domaines, alias, hôtes canoniques, document roots et proxies restent des sujets distincts du modèle cible ; cette passe ne leur ajoute pas de moteur d’exécution. Les liens ROOT vers DNS, certificats et runtimes conservent la séparation des portails.

Le catalogue accepte désormais des mots de recherche optionnels dans ses entrées. MIME, redirections, cache, compression, HSTS et index conduisent au centre existant via `WCPNavigation.matches`, sans ajouter d’entrées concurrentes.

## Politique publiée par ROOT

Clé : `wcp360-web-policy-v1`, schéma 1. Le brouillon et la politique publiée sont des copies distinctes, avec révisions et génération.

ROOT peut publier :

- un sous-ensemble des statuts 301/302/307/308 ;
- un maximum de 1 à 5 sauts de règles exactes ;
- l’autorisation des destinations HTTPS externes, de la prévisualisation de listing et de la compression ;
- l’autorisation de la préférence HSTS et l’obligation HTTPS pour les nouvelles publications ;
- un plafond de cache de 0 à 86 400 secondes ;
- un sous-ensemble fermé de types MIME de démonstration.

La revue exige une raison et la saisie de `WEB POLICY`. Le job publie le snapshot capturé après réussite et conserve les modifications de brouillon suivantes. Une annulation conserve la politique active. Une publication devenue obsolète ne remplace pas une autre politique publiée entre-temps.

Sans état ROOT enregistré, les contraintes de démonstration sont utilisées. Un état explicite illisible, de schéma non reconnu ou dont les types sont malformés bloque les nouvelles modifications/publications USER. Une chaîne « false » n’est pas acceptée comme un booléen autorisant des destinations externes. Le schéma ROOT n’est réparé qu’après une publication revue, pas à la simple ouverture de la page.

Une politique plus stricte ne réécrit pas automatiquement les configurations déjà appliquées. Elle invalide les anciennes preuves et s’applique à la prochaine validation/publication ou restauration.

## Brouillon du site et sous-ensemble de règles

Clé : `wcp360-account-web-rules-v1`. Chaque site possède un brouillon, un snapshot appliqué, des révisions, une génération, des validations et des versions conservées. L’inventaire et le propriétaire viennent de `website-model.js`. Un identifiant explicite inconnu ne sélectionne pas un autre site.

Les redirections correspondent à un chemin exact sur le hostname primaire du site. Les jokers, regex, substitutions et fragments de configuration serveur ne sont pas acceptés. Les destinations sont des chemins locaux simples ou, si ROOT le permet, des URL HTTPS sans credentials, port personnalisé, query ni fragment. Les adresses IP littérales et les chemins traversants/échappés ne font pas partie de ce sous-ensemble.

Les labels sont uniques par site. Deux règles actives ne peuvent pas correspondre au même chemin. Le graphe refuse les boucles, auto-redirections et chaînes qui dépassent la limite ROOT. Les règles désactivées ne participent pas à ce graphe. Le champ Preserve query conserve la query d’entrée dans la destination calculée.

Les types MIME sont choisis dans la liste ROOT. Les extensions exécutables et de configuration ne peuvent pas être remappées ; aucun chemin d’exécutable, commande ou handler arbitraire n’est accepté. Le runtime reste géré dans Application settings.

Les options couvrent HTTPS/HSTS, listing, noms d’index, compression, durée de cache et un filtre de referrer de démonstration pour les images. Les noms d’index restent des noms de fichiers, sans chemin. Le filtre de referrer ne constitue pas un contrôle d’accès et n’accorde aucun droit de bypass.

## Validation, publication et restauration

Les modifications du brouillon ne changent pas le snapshot appliqué et ne créent pas de job. La suppression d’une règle est confirmée et reste dans le brouillon.

Validate draft vérifie réellement les contrats locaux et le graphe, puis enregistre un résultat de syntaxe moteur explicitement simulé après un job. La preuve capture la révision, la génération appliquée, l’identité du site, la politique ROOT et le statut du binding TLS. Une preuve d’une autre version ou d’un autre contexte ne peut pas servir à publier. Un échec récent remplace un résultat réussi plus ancien du même contexte ; une publication en attente ne peut pas consommer une preuve devenue obsolète ou remplacée.

La revue de publication décrit les ajouts, suppressions et préférences modifiées. Elle exige une raison, un résultat d’activation simulé et, en production, la saisie exacte du hostname. Un seul job de publication/restauration peut être actif par site. Les contraintes et la preuve sont vérifiées à nouveau avant application.

Seul un job terminé avec succès et un résultat « Pass » applique son snapshot. Un échec, une annulation, une politique changée, un certificat renouvelé sans déploiement ou un site supprimé laisse la configuration active intacte. Les éditions de brouillon suivantes sont conservées. Les résultats sont appliqués une seule fois, y compris au chargement différé du Job Center.

Les versions appliquées peuvent être restaurées après revue. La restauration relit les contraintes et le TLS actuels et ne remplace pas le brouillon. L’abandon du brouillon rétablit le snapshot appliqué après confirmation du hostname et est bloqué pendant une publication.

## Réutilisation de SSL, Sites et des checks

`WCPDNSTLSSchemas.bindingStatus(state, hostname)` fournit une projection commune de disponibilité HTTPS. Elle exige un certificat du compte, valide dans les métadonnées, non expiré/révoqué, couvrant le hostname et dont la génération courante est liée au site. Des générations manquantes ou des SANs malformés ne sont pas considérés comme prêts. Les anciennes chaînes d’identifiant de binding restent compatibles avec la génération 1.

Le centre SSL enregistre maintenant son inventaire initial de démonstration à l’ouverture. En l’absence d’inventaire, Sites affiche « Not verified » et invite à inspecter SSL. Les badges statiques « Covered » et compteurs de jours ne font plus foi dans ces vues. Le statut « Bound (demo) » reste une métadonnée du prototype, pas une vérification X.509 réelle.

Une publication web met à jour les préférences HTTPS, HSTS et l’indicateur de cache de l’inventaire commun après réussite. Les sauvegardes de Sites préservent ces champs. Les boutons de redirection/HTTPS et le second formulaire de cache ont été remplacés par des liens vers Web Compatibility, avec le site sélectionné dans Manage. L’ancien onglet Policies de Sites est devenu un répertoire des centres propriétaires pour TLS, DNS, règles web, applications, journaux, versions, sauvegardes et accès fichiers ; ses préférences concurrentes inopérantes ont été retirées.

Les observations HTTP de Application settings capturent aussi la génération des règles web appliquées. Un nouveau déploiement de règles rend les anciennes observations obsolètes et invalide un check encore en cours. Une édition de brouillon seule ne change pas cette génération.

Preview request calcule un parcours local depuis une URL du hostname primaire. Le résultat montre les sauts, la query conservée ou retirée et les métadonnées de réponse. Il ne suit pas la destination, ne vérifie pas les fichiers et n’émule pas les changements de méthode d’un navigateur. Les aperçus JSON utilisent du texte et peuvent se replier sur les petits conteneurs.

## Vérification et limites

```powershell
node WCP360_Prototype/tests/audit_wcp360_web_rules.mjs
node WCP360_Prototype/tests/audit_wcp360_observability.mjs
node WCP360_Prototype/tests/audit_wcp360_applications.mjs
node WCP360_Prototype/tests/audit_wcp360_delivery.mjs
node WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs
node WCP360_Prototype/tests/audit_wcp360_protection.mjs
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Résultats : **54 nouveaux scénarios** — 51 pour les règles/politiques, deux pour les observations HTTP et un pour les liens/statuts de Sites — soit **369 tests de comportement réussis**. L’intégration vérifie **232 pages, 55 scripts, 2 795 références locales, 58 redirections et 4 064 assertions**. L’audit statique ne relève aucun écart ; les 55 scripts sont compilables. Foundation conserve 167 consommateurs HTML complets. Les deux centres, Sites, SSL et les scripts vérifiés répondent en HTTP 200.

Les tests exécutent les vrais contrôleurs et schémas avec des adaptateurs aux frontières du DOM. Le connecteur navigateur était indisponible : rendu, petits écrans, focus des dialogues et téléchargements restent à vérifier visuellement. Aucun moteur web, fichier de configuration, endpoint, certificat réel ou service n’est modifié. Les validations et états localStorage ne remplacent ni autorisation serveur, ni transactions backend, ni tests de syntaxe d’un moteur web réel.
