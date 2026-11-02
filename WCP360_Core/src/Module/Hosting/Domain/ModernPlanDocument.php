<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Explicit plan/3 preview; never inferred from a stored Worker engine assignment. */
#[Exclude]
final readonly class ModernPlanDocument
{
    /** @param array<string,string> $fields */
    private function __construct(private array $fields) {}

    public static function decode(string $json): self { return self::fromArray(PlanDocumentFields::decode($json)); }

    /** @param array<array-key,mixed> $fields */
    public static function fromArray(array $fields): self
    {
        $checked = PlanDocumentFields::check($fields,
            ['schema','web_stack','application_server','caddy','systemd_unit'], 'wcp360.candidate.plan/3');
        if ($checked['web_stack'] !== 'modern' || $checked['application_server'] !== 'frankenphp_worker') {
            throw new \InvalidArgumentException('Candidate Modern plan rejected.');
        }
        return new self($checked);
    }

    /** @return array<string,string> */
    public function fields(): array { return $this->fields; }

    /** @return array<string,array{label:string,name:string,content:string}> */
    public function files(): array
    {
        return [
            'caddy' => ['label' => 'Caddy / FrankenPHP direct', 'name' => 'Caddyfile', 'content' => $this->fields['caddy']],
            'systemd' => ['label' => 'Service Modern', 'name' => 'frankenphp-modern.service', 'content' => $this->fields['systemd_unit']],
        ];
    }
}
