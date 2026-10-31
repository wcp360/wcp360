# Modèle de sécurité — alpha

## Périmètre pris en charge

Un propriétaire authentifié, un tenant fixe `standalone`, un répertoire privé et non servi par le serveur HTTP, inaccessible en écriture à d’autres processus/utilisateurs. Le processus PHP n’est jamais root. Le répertoire `state` est privé, distinct de la racine et sur le même système de fichiers pour les déplacements vers la corbeille.
L’identité et la racine proviennent de la configuration serveur. Le navigateur ne choisit ni tenant, ni UID, ni racine système.

## Mesures implémentées

- Chemins relatifs ; refus de `..`, `.`, segments vides, chemins absolus, antislash, caractères de contrôle, `:` et encodages de noms non UTF-8.
- Test d’appartenance avec frontière `/` : `/root-other` n’appartient pas à `/root`.
- Inspection `lstat` de chaque composant ; refus de tous les liens symboliques (même internes ou pendants), fichiers spéciaux et fichiers réguliers possédant plusieurs liens physiques.
- Création uniquement dans un parent existant et validé. Racine non modifiable.
- Contrôle du tenant et du sujet dans le backend, contrôle écriture côté serveur ; rôle seul insuffisant pour choisir une racine.
- Verrou interprocessus partagé par toutes les opérations du module ; écriture temporaire puis renommage, permissions ordinaires conservées sans bits setuid/setgid.
- Révision SHA-256 obligatoire pour enregistrer ou envoyer un fichier à la corbeille. Conflit = HTTP 409 `CONCURRENCY_CONFLICT`.
- CSRF obligatoire pour toute mutation, y compris upload et logout ; session HttpOnly et SameSite Strict ; cookie Secure sur HTTPS.
- Login throttling, 5 tentatives par minute selon la stratégie Symfony configurée.
- Fichiers servis en `application/octet-stream` avec `attachment` et `nosniff`. Aucun rendu HTML/SVG/PHP utilisateur.
- UI insérant les noms et messages par `textContent`, jamais `innerHTML` ; CSP restrictive, cache interdit.
- Journal dédié : sujet, tenant, opération, résultat, request ID. Pas de contenu, mot de passe, token, chemin absolu ou message d’exception brut dans ce journal.

## Limites à ne pas confondre avec des garanties

`realpath/lstat` puis ouverture est vulnérable aux courses si un acteur externe peut changer l’arborescence entre vérification et usage. Le verrou ne protège que les appels coopératifs de ce module. Le backend local est **inadapté aux homes d’hébergement hostiles ou modifiables par PHP, SSH, FTP, cron ou un autre gestionnaire**. Il ne faut pas l’activer pour WCP360 multi-tenant.
Pour ce contexte : daemon/exécuteur par tenant, privilèges minimaux, confinement OS, opérations relatives à des descripteurs (par exemple openat2 avec RESOLVE_BENEATH/NO_SYMLINKS selon contrat validé), contrôle des hardlinks/mounts et réautorisation à l’exécution.
L’écriture est atomique du point de vue de la visibilité ; aucune garantie de durabilité après coupure électrique n’est revendiquée (pas de fsync du fichier et du répertoire).
Les renommages de dossiers ne transportent pas une révision de tout leur arbre. Les ACL, xattrs et propriétaires complexes ne sont pas préservés par le remplacement atomique ; ce backend est destiné à ses fichiers privés simples.
Un échec après mutation (par exemple audit ou réponse HTTP) peut laisser une opération déjà appliquée. Pas de retry automatique des écritures ; vérifier l’état avant de réessayer. Un journal durable avec idempotence appartient au contrat WCP360 futur.

## Exploitation

Quotas disque au niveau OS, journal avec rotation/rétention, sauvegardes et essai de restauration, HTTPS, restriction d’accès et mises à jour contrôlées. Sauvegarder simultanément fichiers et state hors des opérations en cours. Ne pas mettre state dans la racine gérée. Ne pas partager state entre deux installations indépendantes.
Les logs du reverse proxy doivent aussi éviter les query strings sensibles. L’audit applicatif n’est ni immuable ni signé.

## Corrections par rapport à l’exemple initial

Préfixe délimité, absence d’exposition des chemins système, création validée, rejet des liens pendants et hardlinks, contrôle de concurrence, écriture atomique, limites mémoire, JSON strict, erreurs sans fuite, protection CSRF et séparation d’identité. Une liste d’extensions ne constitue pas un mécanisme d’isolation.

## Delta 0.2.0 — copies et arborescences

Toute copie récursive est prévalidée sous verrou : liens symboliques, hardlinks et fichiers spéciaux sont refusés sur chaque composant ; profondeur, taille, nombre d’éléments sont bornés. Le staging créé dans le dossier de destination n’est visible comme destination finale qu’après renommage ; il est nettoyé en cas d’échec normal. Un arrêt brutal peut laisser un staging `.fm-copy-*` à nettoyer après diagnostic, sans reprendre automatiquement une copie interrompue.
Les copies créent des fichiers privés 0600 et dossiers 0700 sur Linux. Sur Windows, chmod ne fournit pas une politique ACL complète ; l’utilisation reste limitée au test local dans un dossier privé.
Les dossiers non vides sont envoyés à la corbeille par renommage après validation de leur arborescence. L’identité et la révision d’un dossier complet ne sont pas persistées ; le modèle exclusif d’écriture reste obligatoire.
La sélection multiple ne change pas les autorisations serveur. Chaque appel individuel est authentifié, autorisé et protégé par CSRF. Les actions déjà réussies ne sont pas annulées par un échec ultérieur.

## Delta 0.3.0

Lire PRO-TOOLS.md pour les nouvelles surfaces : CHMOD borné, quotas applicatifs, état privé de versions/verrous/partages, route publique de téléchargement, rotation d’audit et purge définitive. Le backend local conserve ses préconditions d’écriture exclusive. L’ajout de ces outils ne valide ni un usage multi-tenant ni l’exposition publique sans HTTPS/hardening de l’hôte.
Les capacités d’administration sont distinctes du droit d’écriture et refusées par défaut dans Identity. Les requêtes de recherche passent en POST et les mots de passe/tokens ne sont pas inscrits dans l’audit du module. Les logs HTTP externes doivent masquer les URL de partage et ne pas capturer les corps de requêtes.
