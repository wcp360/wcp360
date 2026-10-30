# V4.0.21.10 --- File Manager Deep Contract

## Racines

Chaque opération porte `account_id + managed_root_id + relative_path`.
Aucun chemin absolu fourni par WCP-USER n'est une autorité.

## Opérations

list/stat/create-file/create-dir/read-range/write/rename/copy/move/delete-to-trash/
restore-trash/chmod-safe/upload-init/upload-part/upload-complete/download-ticket/
preview/editor-save/search/archive-create/archive-extract.

## Garde-fous

-   normalisation Unicode et séparateurs ;
-   interdiction NUL/control chars ;
-   profondeur et longueur bornées ;
-   anti `..`, symlink/hardlink escape ;
-   protection FIFO/socket/device node ;
-   upload multipart avec taille totale et nombre de fichiers ;
-   détection MIME côté serveur ;
-   preview active content isolée ;
-   editor save avec ETag/version pour éviter écrasement concurrent ;
-   extraction d'archive avec budget bytes/files/ratio/profondeur ;
-   trash soumis au quota et à la rétention ;
-   permissions limitées à une matrice sûre, jamais setuid/setgid
    arbitraire.

## Monaco

Monaco est un composant UI ; il ne donne aucun droit supplémentaire.
`.env`, clés privées, fichiers credentials et fichiers système sensibles
suivent une politique de lecture/édition distincte.
