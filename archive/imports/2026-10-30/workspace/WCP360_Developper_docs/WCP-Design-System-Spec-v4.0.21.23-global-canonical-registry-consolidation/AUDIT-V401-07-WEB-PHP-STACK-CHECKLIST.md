# V4.0.1 --- Web Php Stack Checklist

Auditer séparément Nginx, Apache, Nginx→Apache, LiteSpeed/OpenLiteSpeed.
Pour chaque mode : vhost ownership, templates, config test, activation
atomique, reload, rollback, logs, HTTP/2, HTTP/3 si retenu, WebSocket,
compression, redirects, headers, error pages, proxy rules, caching,
WAF/ModSecurity, PHP-FPM pools,
versions/extensions/php.ini/opcache/limits/isolation. Aucun fragment
root arbitraire depuis l'UI.
