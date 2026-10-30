# Outils Pro — 0.3.0 alpha

## Périmètre et autorisations

Mode local mono-propriétaire exclusivement contrôlé par le module. Aucun accès root et aucune commande shell. Le backend local reste interdit pour les homes multi-tenant modifiables par des processus externes. Le bouton Options est une organisation de l’interface, pas un mécanisme d’autorisation.
`Identity` ajoute `canManage=false` par défaut. CHMOD, quota, audit et purge définitive exigent ce droit dans le backend. Le provider autonome l’accorde au rôle `ROLE_FILE_MANAGER_ADMIN`, affecté au propriétaire unique de l’installation. `canWrite` reste exigé pour toute mutation. Un adaptateur hôte doit fournir des capacités vérifiées, et non accepter des flags venant du navigateur.

## CHMOD

Linux uniquement. Modes sur trois chiffres octaux, sans setuid/setgid/sticky, sans écriture publique. Le propriétaire conserve lecture/écriture et, pour les dossiers, traversée. Récursion optionnelle avec target=all/files/directories, inventaire borné à 2 000 entrées / 32 niveaux / 100 Mio / 20 Mio par fichier. Prévalidation avant modification ; en cas d’échec système, tentative de restauration des modes déjà changés, avec erreur explicite si cette restauration est incomplète.
Ce n’est pas CHOWN, une gestion d’ACL, ni une élévation de privilèges. Les modes Windows sont indicatifs, pas des ACL NTFS ; CHMOD est refusé côté serveur sur Windows.

## Espace et quota

Mesure des fichiers gérés et du répertoire state (corbeille, versions, copies de partage, audit et métadonnées). Les caches, sessions et logs Symfony hors state ne sont pas comptés. Analyse jusqu’à 20 000 entrées dans chaque zone, sans suivre les liens. Le tableau des plus gros éléments porte sur le dossier actuellement ouvert ; naviguer dans un sous-dossier puis ouvrir Options permet d’affiner l’analyse.
Actualisation du widget à l’ouverture puis toutes les 60 secondes lorsque la page est visible, et à certaines opérations. Alerte visuelle à 85 %, dépassement signalé à 100 %. Pas d’email ni de monitoring extérieur.
Quota applicatif configurable en octets : 0 désactive, sinon minimum 1 Mio. Contrôle avant écritures de contenu, copies, conservation de versions, copies de partage et snapshots temporaires, sous le verrou du module. Marge conservatrice de 64 Kio pour métadonnées. Le journal et les métadonnées peuvent encore croître lors d’un refus : ce contrôle n’est pas un quota disque dur, un quota d’inodes, ni une garantie contre une consommation externe. Les quotas Linux/NTFS et limites d’IO restent à l’autorité système.
Le passage en corbeille ne libère pas d’espace total. La purge définitive de la sélection, réservée à l’administrateur et confirmée dans l’UI, le libère. Pas de purge automatique de corbeille.

## Audit

Journal JSONL privé : horodatage UTC, sujet, tenant, IP reçue par Symfony, request ID, opération, résultat, chemins relatifs et identifiants utiles. Contenus, mots de passe, tokens de verrou/partage et recherches ne sont pas journalisés par le module. L’IP ne provient pas librement de X-Forwarded-For ; l’hôte devra déclarer seulement ses reverse proxies de confiance.
Le panneau affiche les 200 dernières entrées. Deux segments locaux d’environ 5 Mio avec rotation ; pas d’immuabilité, de signature ou de centralisation. Mutations après contrôle d’identité et téléchargements partagés réussis couverts ; les refus précoces d’identité/role et tous les événements d’authentification ne constituent pas ici un audit de sécurité central complet.
Une panne d’audit après mutation peut retourner une erreur alors que les fichiers ont changé. Vérifier l’état avant tout retry ; pas d’idempotence durable dans cette alpha.

## Recherche

POST JSON + CSRF pour éviter d’inscrire un token recherché dans l’URL. Lecture seule autorisée même sans canWrite. Recherche littérale UTF-8 insensible à la casse, sans regex ni shell. Limites : 5 000 entrées, 20 Mio cumulés lus, 1 Mio par fichier, 200 résultats. Indication explicite si résultats partiels. Les fichiers binaires, invalides UTF-8 ou trop grands sont ignorés. Les réponses contiennent chemin/ligne, sans extrait de secret.

## Versions, instantanés et sauvegardes

Avant chaque save accepté, copie de l’état précédent dans state/versions. Maximum 20 versions par chemin ; les plus anciennes sont supprimées après création d’une nouvelle version. La restauration exige la révision SHA-256 actuelle et les droits d’écriture, puis sauvegarde à son tour l’état précédent. Fichier courant requis ; pour un fichier supprimé, restaurer d’abord la corbeille. Les versions sont liées au chemin, ne suivent pas les renommages et ne représentent pas l’identité immuable d’un inode. L’édition/restauration textuelle reste limitée à 1 Mio.
Un snapshot ZIP est une archive ponctuelle à télécharger, non conservée sur le serveur après réponse. Fichier ou dossier unique, racine complète interdite. Extension PHP zip requise ; sources bornées aux mêmes limites que les copies, ZIP final ≤20 Mio. Pas d’extraction ni de TAR/GZ. Quota vérifié conservativement avant staging temporaire.
Versions locales et corbeille ne remplacent pas des sauvegardes hors serveur. Aucun snapshot filesystem/ZFS/Btrfs ni catalogue de sauvegardes distant n’est implémenté.

## Partage

Fichier uniquement, ≤20 Mio, copie immuable privée. Token aléatoire 256 bits ; seul son SHA-256 sert d’identifiant stocké. Mot de passe obligatoire (au moins 12 caractères UTF-8, maximum 256 octets), condensé en SHA-256 puis haché avec password_hash pour éviter la troncature bcrypt au-delà de 72 octets. Expiration entre 60 secondes et 24 heures ; maximum 5 téléchargements réussis ; limite de 5 essais de mot de passe échoués par minute et par lien. Maximum 100 liens actifs. Révocation disponible. Nettoyage des copies expirées/épuisées à la prochaine consultation/création de partage, pas via cron autonome.
Route publique autonome `/s/{token}` : GET présente le formulaire ; POST avec mot de passe renvoie une pièce jointe octet-stream + nosniff, sans cookie de session de panel requis. Aucune création de partage sans session, CSRF et permission d’écriture. Le token n’est révélé qu’à la création ; mot de passe transmis séparément.
Sur loopback, le lien n’est accessible que depuis le PC local. Pour le rendre accessible à un tiers : déploiement HTTPS contrôlé nécessaire. Ne pas journaliser les tokens `/s/...` ni les corps de requêtes dans le reverse proxy. Le formulaire public est volontairement séparé de la session du panel. Les liens existants continuent d’accéder à leur copie même si le fichier source change, jusqu’à révocation/expiration.

## Verrous

Verrou explicite dans l’éditeur, token aléatoire conservé uniquement en mémoire de l’onglet. Sauvegarde avec ce token, blocage des mutations concurrentes sans token et des mutations de dossiers contenant un fichier verrouillé. Expiration 15 minutes, renouvellement toutes les 60 secondes si l’éditeur et la page restent visibles. Libération à la fermeture normale de l’éditeur ; crash/perte réseau traités par expiration.
Ils sont coopératifs, entre appels de ce backend. Pas de verrou OS imposé à SSH/FTP/cron. L’application autonome ne gère pas plusieurs comptes d’équipe ; la collaboration multi-utilisateur exige l’identité, les scopes et le stockage partagés de l’hôte. L’optimistic concurrency SHA-256 reste active même avec un verrou.

## Opérations ajoutées au port FileBackend

| Méthode HTTP | Opération | Entrées principales |
|---|---|---|
| GET | capabilities | aucune |
| GET | disk_usage | path optionnel pour la répartition |
| POST | quota_set | bytes entier, admin |
| POST | chmod | path, mode, recursive booléen, target all/files/directories, admin |
| POST | search | path, query, lecture seule + CSRF |
| GET | versions | path |
| POST | version_restore | path, id, revision, lock_token si verrou |
| POST | snapshot | path ; réponse binaire attachment |
| POST | lock_acquire | path |
| POST | lock_renew / lock_release | path, lock_token |
| POST | share_create | path, password, ttl en secondes |
| GET | share_list | aucune ; ne révèle pas les tokens ni les hashes de mots de passe |
| POST | share_revoke | id |
| GET | audit | admin |
| POST | trash_purge | id de corbeille, admin |

Codes supplémentaires : ADMIN_REQUIRED (403), UNSUPPORTED_PLATFORM (409), INVALID_MODE (400), MODE_NOT_ALLOWED (403), CHMOD_FAILED / CHMOD_ROLLBACK_INCOMPLETE (500), QUOTA_EXCEEDED (413), SCAN_LIMIT (413), ZIP_UNAVAILABLE (409), ZIP_FAILED (500), FILE_LOCKED (423), LOCK_TOKEN_INVALID (409), LOCK_LIMIT / SHARE_LIMIT (413), SHARE_DENIED (403), SHARE_THROTTLED (429), AUDIT_FAILED (500).

## WCP360 : fonctions restant à contractualiser

Git clone/pull et webhooks doivent passer par des jobs typés, dépôts allowlistés, secrets dédiés, politiques réseau et absence d’exécution de hooks arbitraires. Aucun exécuteur Git n’est fourni ici.
Les symlinks restent interdits : les autoriser avec un simple realpath contredirait l’isolation locale. Leur support exige une politique explicite et des opérations descriptor-relative dans l’exécuteur Linux.
CHOWN, quotas Linux, SSH/terminal, UID/GID par tenant, isolation multiserveur, jobs durables et sauvegardes distantes restent à l’adaptateur WCP360. Le panneau Intégration présente ces états ; il ne simule pas leur activation.
