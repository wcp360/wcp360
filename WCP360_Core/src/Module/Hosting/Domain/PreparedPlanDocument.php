<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Bounded candidate document, not configuration syntax acceptance or permission to apply. */
#[Exclude]
final readonly class PreparedPlanDocument
{
    public const MAX_CONTENT_BYTES = 65536;
    public const MAX_JSON_BYTES = 6 * self::MAX_CONTENT_BYTES;

    /** @param array<string,string> $fields */
    private function __construct(private ApplicationServer $server, private array $fields) {}

    public static function decode(string $json, ApplicationServer $server): self
    {
        return self::fromArray(PlanDocumentFields::decode($json), $server);
    }

    /** @param array<array-key,mixed> $fields */
    public static function fromArray(array $fields, ApplicationServer $server): self
    {
        $fpm = $server === ApplicationServer::PhpFpm;
        $expected = $fpm ? ['schema', 'nginx', 'php_fpm'] : ['schema', 'nginx', 'application_server', 'caddy', 'systemd_unit'];
        $checked = PlanDocumentFields::check($fields, $expected, $fpm ? 'wcp360.candidate.plan/1' : 'wcp360.candidate.plan/2');
        if (!$fpm && $checked['application_server'] !== $server->value) self::invalid();

        return new self($server, $checked);
    }

    /** @return array<string,string> Original planner field order/shape retained for existing callers. */
    public function fields(): array { return $this->fields; }

    /** @return array<string,array{label:string,name:string,content:string}> */
    public function files(): array
    {
        $files = ['nginx' => ['label' => 'Nginx', 'name' => 'nginx.conf', 'content' => $this->fields['nginx']]];
        if ($this->server === ApplicationServer::PhpFpm) {
            $files['php-fpm'] = ['label' => 'PHP-FPM', 'name' => 'php-fpm.conf', 'content' => $this->fields['php_fpm']];
        } else {
            $files['caddy'] = ['label' => 'FrankenPHP / Caddy', 'name' => 'Caddyfile', 'content' => $this->fields['caddy']];
            $files['systemd'] = ['label' => 'Service FrankenPHP', 'name' => 'frankenphp.service', 'content' => $this->fields['systemd_unit']];
        }

        return $files;
    }

    private static function invalid(): never
    {
        throw new \InvalidArgumentException('Candidate plan document rejected.');
    }
}
