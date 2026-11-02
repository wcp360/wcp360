<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Domain;

/** Fixed observation references only; a catalogue entry never admits service control.
 * @phpstan-type Entry array{id:string,label:string,owner:string,units:list<string>}
 */
final class ServiceUnitCatalogue
{
    /** @return list<Entry> */
    public static function entries(): array
    {
        return [
            ['id' => 'nginx', 'label' => 'Nginx', 'owner' => 'Hosting', 'units' => ['nginx.service']],
            ['id' => 'apache', 'label' => 'Apache', 'owner' => 'Hosting', 'units' => ['apache2.service']],
            ['id' => 'php_fpm', 'label' => 'PHP-FPM', 'owner' => 'Hosting',
                'units' => ['php8.1-fpm.service','php8.2-fpm.service','php8.3-fpm.service','php8.4-fpm.service','php8.5-fpm.service']],
            ['id' => 'caddy', 'label' => 'Caddy', 'owner' => 'Hosting', 'units' => ['caddy.service']],
            ['id' => 'frankenphp', 'label' => 'FrankenPHP', 'owner' => 'Hosting', 'units' => ['frankenphp.service']],
            ['id' => 'postgresql', 'label' => 'PostgreSQL', 'owner' => 'Data', 'units' => ['postgresql.service']],
            ['id' => 'mysql', 'label' => 'MySQL/MariaDB', 'owner' => 'Data', 'units' => ['mysql.service','mariadb.service']],
            ['id' => 'redis', 'label' => 'Redis', 'owner' => 'Operations', 'units' => ['redis-server.service']],
            ['id' => 'docker', 'label' => 'Docker', 'owner' => 'Platform', 'units' => ['docker.service']],
        ];
    }

    /** @return list<string> Stable order, including each fixed unit reference once. */
    public static function unitNames(): array
    {
        $names = [];
        foreach (self::entries() as $entry) {
            foreach ($entry['units'] as $unit) {
                if (!in_array($unit, $names, true)) $names[] = $unit;
            }
        }
        return $names;
    }
}
