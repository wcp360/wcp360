<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;

/** Explicit Application preview envelope; never inferred from a persisted site or Worker assignment. */
#[Exclude]
final readonly class ApplicationPlanDocument
{
    private const ARTIFACTS = [
        'nginx' => ['label' => 'Nginx / Application', 'name' => 'nginx-application.conf'],
        'apache' => ['label' => 'Apache / Application', 'name' => 'apache-application.conf'],
        'caddy' => ['label' => 'Caddy / Application', 'name' => 'Caddyfile'],
        'php_fpm' => ['label' => 'PHP-FPM / Application', 'name' => 'php-fpm-application.conf'],
        'systemd_unit' => ['label' => 'FrankenPHP classic / Application', 'name' => 'frankenphp-application.service'],
        'htaccess' => ['label' => 'Application .htaccess', 'name' => '.htaccess'],
    ];

    /** @param array<string,string> $fields */
    private function __construct(private array $fields) {}

    public static function decode(string $json, string $recipe, string $application): self
    {
        return self::fromArray(PlanDocumentFields::decode($json), $recipe, $application);
    }

    /** @param array<array-key,mixed> $fields */
    public static function fromArray(array $fields, string $recipe, string $application): self
    {
        ApplicationRecipeCatalogue::requireProfile($application);
        $choice = ApplicationRecipeCatalogue::choice($recipe);
        $checked = PlanDocumentFields::check($fields,
            ['schema', 'application', 'recipe', 'web_server', 'application_server', ...$choice['artifacts']],
            'wcp360.candidate.application-plan/1');
        if ($checked['application'] !== $application || $checked['recipe'] !== $choice['id'] || $checked['web_server'] !== $choice['web_server']
            || $checked['application_server'] !== $choice['application_server']) {
            throw new \InvalidArgumentException('Candidate Application plan rejected.');
        }
        return new self($checked);
    }

    /** @return array<string,string> Original field order and content bytes. */
    public function fields(): array { return $this->fields; }

    /** @return array<string,array{label:string,name:string,content:string}> */
    public function files(): array
    {
        $files = [];
        foreach (ApplicationRecipeCatalogue::choice($this->fields['recipe'])['artifacts'] as $artifact) {
            $descriptor = self::ARTIFACTS[$artifact] ?? throw new \LogicException('Unknown Application recipe artifact.');
            $files[$artifact] = [...$descriptor, 'content' => $this->fields[$artifact]];
        }
        return $files;
    }
}
