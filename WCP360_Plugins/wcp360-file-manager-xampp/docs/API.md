# API de l’extension — v1

Base `/api/v1/files`. Authentification par session Symfony ; identité exclusivement serveur. Racine représentée par `""`, jamais `/`. Noms UTF-8, chemins relatifs, limite 4 096 octets (segment 255).
GET de session fournit `csrf`, `logout_csrf`, `user`, `can_write`. Les POST JSON exigent `Content-Type: application/json` et `X-CSRF-Token`; upload utilise multipart.

| Méthode | Opération | Paramètres | Résultat data |
|---|---|---|---|
| GET | session | aucun | utilisateur, capacités et CSRF |
| GET | list | path, page=1, query optionnel | path, items, total, page, page_size |
| GET | read | path | name, path, content, revision |
| GET | download | path | octets, Content-Disposition attachment |
| POST | create | path, content | path, revision |
| POST | save | path, content, revision | path, revision |
| POST | upload | path + file multipart | path, revision |
| POST | mkdir | path | path |
| POST | rename | path, destination | path |
| POST | trash | path, revision pour un fichier | id, path |
| GET | trash_list | aucun | items : id, path, deleted_at |
| POST | restore | id | path |

Une entrée liste contient `name`, `path`, `kind` (file/directory/blocked), `size`, `mtime` Unix et `mode`, `uid`, `gid` (null sous Windows). Aucun chemin système absolu.
Succès JSON : `{ "status":"success", "data":{...}, "request_id":"..." }` (sauf session qui retourne seulement data).
Erreur applicative : `{ "error":{"code":"...","message":"..."}, "request_id":"..." }`.

400 : INVALID_INPUT, INVALID_PATH, INVALID_JSON, INVALID_UPLOAD, NOT_A_FILE, NOT_A_DIRECTORY.
403 : ACCESS_DENIED, READ_ONLY, CSRF_INVALID, UNSAFE_ENTRY, ROOT_PROTECTED.
404 : NOT_FOUND, UNKNOWN_OPERATION.
409 : ALREADY_EXISTS, CONCURRENCY_CONFLICT, DIRECTORY_NOT_EMPTY.
413 : FILE_TOO_LARGE, REQUEST_TOO_LARGE, DIRECTORY_TOO_LARGE, TRASH_TOO_LARGE.
415 : NOT_EDITABLE, NOT_TEXT, INVALID_CONTENT_TYPE.
500 : IO_ERROR, INTERNAL_ERROR. 503 : STORAGE_BUSY, WCP_ADAPTER_NOT_CONFIGURED.

Le firewall autonome redirige une session absente vers `/login`. L’interface détecte les redirections et les réponses non JSON. Les erreurs de routing/firewall (404/405/403) peuvent être des réponses Symfony HTML ; elles ne suivent pas l’enveloppe applicative. Un hôte WCP360 devra fournir son entrypoint API JSON canonique.
Pas d’idempotence persistante ni retry automatique pour les mutations dans cette alpha. Aucun endpoint d’exécution ou d’extraction ; CHMOD borné et purge définitive administrateur ajoutés en 0.3.0.

## Delta 0.2.0

GET `properties?path=…` retourne name, path, kind, size, mtime, mode, uid, gid, platform et revision (null pour un dossier ou fichier >20 Mio), sans contenu de fichier ni chemin absolu.
POST `copy` exige path et destination relatifs. Copie fichier/dossier, refus d’écrasement et d’inclusion dans soi-même ; retourne path, entries, bytes.
GET `list` accepte hidden (par défaut true au niveau API pour compatibilité ; l’UI envoie false par défaut) et directories (par défaut false), booléens ou chaînes 0/1/true/false. Le filtre directories est appliqué avant pagination et décompte.
POST `trash` accepte les dossiers non vides après inventaire borné. Le format de corbeille reste compatible avec 0.1.1. La restauration ne modifie pas les parents absents et n’écrase rien.
Erreur 413 `TREE_LIMIT` pour copie/corbeille dépassant 2 000 entrées, 32 niveaux, 100 Mio au total ou 20 Mio par fichier.
Les actions groupées sont orchestrées côté interface par appels successifs aux opérations unitaires ; il n’existe pas de transaction batch ou de rollback global.

## Delta Pro 0.3.0

Contrats, droits et limites dans `PRO-TOOLS.md`. Search est un POST de lecture seule avec CSRF ; snapshot est un POST renvoyant une pièce jointe binaire. Le contrôleur remplace systématiquement le contexte IP/request ID avec les valeurs serveur.
