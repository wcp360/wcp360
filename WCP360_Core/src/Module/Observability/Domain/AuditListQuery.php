<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Observability\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Correlation\RequestId;
use Wcp360\Core\Shared\Http\CursorPageQuery;

#[Exclude]
final readonly class AuditListQuery
{
    /** @param array<string,string> $filters */
    private function __construct(public array $filters, public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input */
    public static function fromArray(array $input): self
    {
        $page = CursorPageQuery::fromArray($input, ['kind', 'actor', 'tenant', 'resource', 'request', 'from', 'until']);
        $filters = [];
        foreach (['kind', 'actor', 'tenant', 'resource', 'request', 'from', 'until'] as $key) $filters[$key] = $input[$key] ?? '';
        if ($filters['kind'] !== '' && preg_match('/\A[a-z][a-z0-9_.]{0,63}\z/D', $filters['kind']) !== 1) CursorPageQuery::invalid();
        foreach (['actor', 'tenant', 'resource'] as $key) {
            if ($filters[$key] !== '') $filters[$key] = CursorPageQuery::uuid($filters[$key]);
        }
        if ($filters['request'] !== '' && !RequestId::valid($filters['request'])) CursorPageQuery::invalid();
        foreach (['from', 'until'] as $key) {
            if ($filters[$key] === '') continue;
            if (preg_match('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z\z/D', $filters[$key]) !== 1) CursorPageQuery::invalid();
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $filters[$key], new \DateTimeZone('UTC'));
            if (!$date || $date->format('Y-m-d\TH:i:s\Z') !== $filters[$key]) CursorPageQuery::invalid();
        }
        if ($filters['from'] !== '' && $filters['until'] !== '' && strcmp($filters['from'], $filters['until']) > 0) CursorPageQuery::invalid();
        $filters['limit'] = (string) $page->limit;

        return new self($filters, $page->limit, $page->after, $page->before);
    }
}
