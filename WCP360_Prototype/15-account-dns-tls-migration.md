# DNS et certificats du compte — 30 octobre 2026

Cette passe rend les surfaces DNS et SSL du portail USER persistantes et réutilise Foundation et `operations-core.js`. Le shell de la baseline V2.5.3.12 est conservé ; la page historique SSL utilise désormais le shell standard déjà présent dans `ssl-tls.html`.

## Centres et routes

| Centre | Propriétaire | Contenu |
| --- | --- | --- |
| `canonical-dns.html` | USER / `example.com` | Brouillon DNS, version publiée, métadonnées DNSSEC, propagation simulée et activité |
| `ssl.html` | USER / `example.com` | Certificats simulés du compte, versions liées aux sites et activité |
| `admin-dns.html` | ROOT | Infrastructure DNS, types autorisés, politique TTL, signatures et fournisseurs |
| `certificates.html` | ROOT | Gouvernance et inventaire des certificats de plateforme et services |

Six pages deviennent des redirections de compatibilité :

| Ancienne route | Destination |
| --- | --- |
| `dns-record-add.html` | `canonical-dns.html?action=add` |
| `dns-record-edit.html` | `canonical-dns.html?action=edit` |
| `dnssec.html` | `canonical-dns.html#dnssec` |
| `dns-propagation.html` | `canonical-dns.html#propagation` |
| `ssl-tls.html` | `ssl.html#certificates` |
| `ssl-certificate-detail.html` | `ssl.html?action=inspect` |

Les paramètres et fragments explicites sont conservés. Les formulaires consomment le paramètre `action` après ouverture. Édition DNS et inspection SSL acceptent un `id` ; un identifiant explicite inexistant est refusé. Une ancienne URL d’édition DNS sans identifiant invite à sélectionner la ligne correcte dans la grille.

La navigation conserve les catégories USER « Domains & DNS » et « SSL / TLS ». Les pages de détail héritent du centre propriétaire, sans ajouter de produit dans la sidebar. Le décorateur `dns-user-policy.js` a été supprimé ; le centre DNS lit lui-même la politique effective.

## Brouillon DNS et publication

- Grilles communes distinctes pour le brouillon et la version publiée : recherche, filtres, tri, colonnes, exports et préférences.
- Création et édition par formulaire partagé. SOA et NS restent gérés par ROOT ; les types désactivés restent visibles et en lecture seule.
- Les noms sont normalisés dans `example.com`. Les champs acceptent `@`, un nom relatif ou un nom absolu terminé par un point. Les noms absolus hors de la zone sont refusés.
- Validation des valeurs A/AAAA, hôtes cibles, champs numériques MX/SRV, CAA, TLSA et SSHFP. Les formats avancés sont limités aux variantes décrites dans l’aide du formulaire ; il ne s’agit pas d’un parseur de fichiers de zone.
- Les doublons, les TTL différents dans un même RRset, les CNAME à l’apex ou coexistants avec d’autres données, les boucles CNAME locales et les cibles MX/SRV qui désignent un CNAME local sont refusés.
- Les types et le TTL minimal sont relus dans `wcp360-dns-center-v1` avant chaque modification et avant application d’une publication terminée. Un TTL minimal ROOT illisible bloque les modifications. Un TTL ROOT égal à zéro est conservé.
- Les enregistrements publiés restés inchangés peuvent être conservés lorsqu’une politique ROOT devient plus stricte ; une nouvelle valeur interdite n’est pas acceptée pour autant.
- L’import ajoute un tableau JSON de 1 à 100 enregistrements. Le lot complet est validé avant mutation, les identifiants sont générés et les attributs de protection fournis dans le JSON ne confèrent aucun privilège.
- Review & publish présente les ajouts, modifications et suppressions avant création du job. Le job capture une copie du brouillon ; sa fin publie cette copie une seule fois. Les modifications de brouillon ultérieures restent intactes. Une annulation laisse la version publiée inchangée.
- Le brouillon ne peut pas être abandonné pendant une publication active. Après annulation ou fin du job, son abandon peut restaurer la dernière version publiée avec confirmation.

La séparation du brouillon et de la publication est couverte par des tests de mutation : leurs tableaux ne partagent pas de référence en mémoire.

Les règles CNAME et TTL s’appuient sur les clarifications de [RFC 2181](https://www.rfc-editor.org/info/rfc2181/), et les champs MX sur [RFC 1035](https://www.rfc-editor.org/info/rfc1035/). Le prototype applique ces validations à ses données locales et ne construit pas de messages DNS.

## DNSSEC et propagation

DNSSEC affiche la politique et les métadonnées de signature ROOT disponibles. Le portail USER ne modifie ni clés, ni DS, ni rollover. Ses jobs de validation et de propagation produisent des observations explicitement simulées, sans interrogation réseau. Une observation capture la révision publiée ; si cette révision change avant la fin du job, le résultat est marqué obsolète.

État métier : `wcp360-account-dns-v1`. Les préférences de grille sont isolées sous USER / `example.com`.

## Cycle des certificats

- L’inventaire du compte est séparé de `wcp360-certificate-center-v1`, qui reste ROOT-owned.
- Les demandes acceptent 1 à 20 SANs appartenant à `example.com`, sans doublons. Les ensembles de SANs et les labels déjà présents ou en attente sont contrôlés.
- Les wildcards doivent utiliser DNS-01, dont la disponibilité dépend de l’autorisation effective des enregistrements TXT. Cette contrainte est vérifiée à la soumission et à la fin du job. Les limites HTTP-01/DNS-01 suivent la documentation officielle des [types de challenges Let’s Encrypt](https://letsencrypt.org/docs/challenge-types/).
- La fin d’un job d’émission crée une seule entrée de métadonnées, avec une validité de démonstration de 30 jours. Un job annulé ou bloqué par une nouvelle politique ne crée aucune entrée.
- Le renouvellement simule une extension de validité de 30 jours et incrémente la version. Les bindings de sites conservent leur ancienne version jusqu’à un job de déploiement distinct ; ils affichent alors « New version needs deployment ».
- Un binding HTTPS exige un nom du compte couvert par les SANs, une chaîne simulée valide et un certificat non révoqué et non expiré. Un wildcard ne couvre qu’un niveau de sous-domaine. Les cibles de plateforme et les services mail restent hors du workflow USER.
- La révocation exige une raison et la saisie exacte du label. La fin du job marque le certificat révoqué, désactive sa préférence de renouvellement et retire ses bindings locaux. Une annulation conserve certificat et bindings.
- Un seul job de cycle de vie peut être actif pour un certificat. Les préférences de renouvellement sont enregistrées sans ordonnanceur autonome.
- Les résultats terminés sont appliqués une seule fois, y compris après rechargement du centre.

État métier : `wcp360-account-certificates-v1`. Aucun certificat utilisable, clé privée, PEM ou credential de fournisseur n’est généré ou stocké par ces nouveaux workflows. Aucune autorité de certification n’est contactée et aucun service réel n’est rechargé.

## Composants et validation

Les contrats métier résident dans `assets/dns-tls-schemas.js`. Les contrôleurs sont `assets/account-dns.js` et `assets/account-certificates.js`. Ils réutilisent les formulaires, dialogues, grilles, thèmes et notifications du socle existant. Les grilles sont détruites avant remplacement de leur onglet. Les onglets disposent des relations ARIA et des commandes flèches / Home / End.

```powershell
node WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs
node WCP360_Prototype/tests/audit_wcp360_protection.mjs
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Résultats : **44 nouveaux tests métier**, soit **180 tests de comportement réussis** avec les suites précédentes. L’intégration contrôle **232 pages, 46 scripts JavaScript, 2 985 références locales et 50 redirections**. Les deux centres et leurs trois nouveaux scripts répondent en HTTP 200. Le sélecteur de type DNS de l’ancien audit navigateur a été adapté au formulaire commun ; cet ancien audit n’a pas été exécuté pendant cette passe.

La vérification visuelle n’a pas été réalisée : le navigateur était utilisé en parallèle et son connecteur de contrôle par onglet était indisponible. Rendu, focus natif, navigation clavier, exports téléchargés et petits écrans restent à vérifier dans un navigateur. Les tests présents utilisent les vrais contrôleurs et schémas avec des adaptateurs aux frontières du DOM ; ils ne constituent pas une validation DNS, TLS ou d’autorisation serveur en production.
