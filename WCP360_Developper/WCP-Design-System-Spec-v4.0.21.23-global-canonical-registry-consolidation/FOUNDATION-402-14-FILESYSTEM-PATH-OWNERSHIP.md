# V4.0.2 --- Filesystem, Paths & Ownership Contract

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Zones

`/opt/wcp` artefacts immuables/versionnés ; `/etc/wcp` configuration ;
`/var/lib/wcp` état durable ; `/run/wcp` runtime/sockets ;
`/var/log/wcp` si fichiers logs ; homes/hosting roots séparés du control
plane.

## Path safety

Aucun composant privilégié n'accepte un path arbitraire utilisateur.
Toute ressource fichier est résolue depuis un FileRoot canonique +
identifiant/relative path validé.

## Symlinks

Politique explicite ; empêcher escape du root, traversal, race TOCTOU et
confusion bind-mount/symlink.

## Ownership

Chaque répertoire déclare owner/group/mode et écrivains autorisés. Les
pools PHP ne peuvent écrire ni release WCP, ni secrets globaux, ni
socket root broker.

## Atomicité

Écriture temp dans le même filesystem → fsync si requis → rename
atomique → permissions/ownership → verify.
