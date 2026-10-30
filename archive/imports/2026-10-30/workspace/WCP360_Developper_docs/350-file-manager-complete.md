# 350 --- File Manager complet

## Origine

**\[V3.1\]** Gestionnaire de fichiers Web complet avec éditeur, archives
et permissions. La documentation WCP-USER prévoyait déjà File
Manager/Git/backup ; ce fichier formalise le niveau fonctionnel complet
demandé.

## Pages

Explorer; Search; Editor; Uploads; Transfers; Archives; Permissions;
Trash si backend le supporte; Activity.

## Explorer

Tree + list, breadcrumb, tri, recherche, hidden files selon permission,
taille/type/date/owner/permissions.

## Actions

Create file/folder; upload; download; rename; move/copy; delete; restore
si trash; chmod; archive/extract `.zip` et `.tar.gz`; calculate size;
copy path.

## Code Editor

Syntax highlighting, line numbers, search/replace, encoding, dirty
state, conflict/version detection, save. Pas de terminal implicite.

## Upload

Chunked/resumable si backend l'implémente; quotas vérifiés avant et
pendant; progression réelle; résultat par fichier.

## Archives

Protection Zip Slip/path traversal; extraction dans scope autorisé;
preview d'impact lorsque possible; limites taille/nombre de fichiers.

## Permissions

Mode symbolique/numérique; owner/group seulement si autorisé. WCP-USER
ne peut pas contourner l'isolation de son compte.

## Sécurité

Canonicalisation des chemins côté backend, protection symlink traversal,
MIME non considéré comme preuve de sécurité, fichiers sensibles
redacted/blocked selon policy.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
