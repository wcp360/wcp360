<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Http\TenantPageQuery;

/** Inventory of prepared candidate sites; state is preparation, not observed deployment health. */
#[Exclude]
final readonly class SiteListQuery
{
    public const STATES = ['PLAN_QUEUED', 'PLAN_READY', 'PLAN_FAILED'];

    private function __construct(public string $tenant, public string $state, public string $search,
        public string $server, public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input */
    public static function fromArray(array $input): self
    {
        $page = TenantPageQuery::fromArray($input, ['state', 'q', 'server']);
        $state = $input['state'] ?? '';
        $server = $input['server'] ?? '';
        if (($state !== '' && !in_array($state, self::STATES, true))
            || !in_array($server, ['', ApplicationServer::PhpFpm->value, ApplicationServer::FrankenPhpWorker->value], true)) {
            TenantPageQuery::invalid();
        }

        return new self($page->tenant, $state, TenantPageQuery::hostnameFragment($input['q'] ?? ''), $server,
            $page->limit, $page->after, $page->before);
    }

    /** @return array<string,string> */
    public function filters(): array
    {
        return ['tenant' => $this->tenant, 'state' => $this->state, 'q' => $this->search,
            'server' => $this->server, 'limit' => (string) $this->limit];
    }
}
