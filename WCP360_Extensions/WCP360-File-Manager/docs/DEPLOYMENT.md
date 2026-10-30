# Déploiement et exploitation

## Serveur local

Exécuter `composer install`, puis l’assistant CLI. PHP 8.2+ accepté pour le test autonome, PHP 8.4 cible WCP360 ; le setup vérifie cette version. Pour un dossier de fichiers existant, choisir seulement un répertoire privé dont l’application est l’unique writer. Configurer `FM_ROOT` et `FM_STATE` dans `standalone/var/config.php`, hors de public. Les deux chemins doivent exister, être disjoints et idéalement des voisins sur le même filesystem.

## PHP-FPM / Nginx

Compte système dédié non root, dossier public égal à `standalone/public` et **jamais** à la racine du paquet ou au dossier géré. Exemple à adapter à votre domaine, socket PHP et certificats :

```nginx
server {
    listen 443 ssl;
    server_name files.example.net;
    # ssl_certificate / ssl_certificate_key à configurer
    root /srv/file-manager/standalone/public;
    client_max_body_size 22m;
    location / { try_files $uri /index.php$is_args$args; }
    location = /index.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_pass unix:/run/php/file-manager.sock;
    }
    location ~ \.php$ { return 404; }
    location ~ /\. { deny all; }
}
```

Dans le pool : `upload_max_filesize=20M`, `post_max_size=22M`, `memory_limit=128M` au minimum, `display_errors=Off`, sessions dans un dossier privé. Ne pas donner de droit root, sudo ni accès au socket daemon avant validation du contrat. Ajouter un quota disque et superviser espace libre et corbeille.
HTTPS obligatoire en réseau ; si TLS est terminé par proxy, configurer des trusted proxies exacts dans l’hôte pour que Symfony reconnaisse HTTPS. Ne jamais faire confiance à tout `X-Forwarded-*` public. L’exemple ne constitue pas une configuration TLS complète.

## Identifiants et sauvegardes

Le setup génère le mot de passe. Pour le changer, générer un hash `password_hash` hors du navigateur, remplacer `FM_PASSWORD_HASH` dans la configuration privée puis vider le cache Symfony et vérifier la révocation des sessions. Ne jamais passer le mot de passe en argument visible dans la liste des processus. Configurer une durée de session conforme à votre usage.
Sauvegarder les fichiers, `state` et la configuration de manière chiffrée avec accès restreint ; arrêter les écritures pendant le snapshot. Tester la restauration. La corbeille conserve indéfiniment : purge administrateur contrôlée et sauvegarde préalable nécessaires.

## Mise à jour

Conserver `var` hors des remplacements de code, arrêter les écritures, sauvegarder, installer la nouvelle version et ses dépendances figées, lancer les tests, réchauffer le cache. En rollback, restaurer le code et son lock, et éventuellement le snapshot de données cohérent. Aucune migration automatique de format de corbeille n’est fournie dans cette alpha.
