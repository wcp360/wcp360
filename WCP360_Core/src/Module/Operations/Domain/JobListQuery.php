<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Http\TenantPageQuery;

/** Bounded candidate read filters; a cursor is a position, never resource authority. */
#[Exclude]
final readonly class JobListQuery
{
    public const STATES = ['QUEUED', 'RUNNING', 'SUCCEEDED', 'FAILED', 'UNKNOWN_OUTCOME'];

    private function __construct(public string $tenant, public string $state, public string $search,
        public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input */
    public static function fromArray(array $input): self
    {
        $page = TenantPageQuery::fromArray($input, ['state', 'q']);
        $state = $input['state'] ?? '';
        if ($state !== '' && !in_array($state, self::STATES, true)) TenantPageQuery::invalid();

        return new self($page->tenant, $state, TenantPageQuery::hostnameFragment($input['q'] ?? ''),
            $page->limit, $page->after, $page->before);
    }

    /** @return array<string,string> Deliberately excludes cursors when filters change. */
    public function filters(): array
    {
        return ['tenant' => $this->tenant, 'state' => $this->state, 'q' => $this->search, 'limit' => (string) $this->limit];
    }
}
