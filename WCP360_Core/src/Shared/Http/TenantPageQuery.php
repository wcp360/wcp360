<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Candidate read-list transport bounds, never a tenant authorization decision. */
#[Exclude]
final readonly class TenantPageQuery
{
    private function __construct(public string $tenant, public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input
     * @param list<string> $extraFields Fixed fields supplied by the owning query, never by the request.
     */
    public static function fromArray(array $input, array $extraFields): self
    {
        $page = CursorPageQuery::fromArray($input, ['tenant', ...$extraFields]);

        return new self(CursorPageQuery::uuid($input['tenant'] ?? ''), $page->limit, $page->after, $page->before);
    }

    public static function hostnameFragment(string $value): string
    {
        if (strlen($value) > 253) self::invalid();
        $search = strtolower(trim($value));
        if (preg_match('/\A[a-z0-9.-]{0,253}\z/D', $search) !== 1) self::invalid();

        return $search;
    }

    public static function invalid(): never { CursorPageQuery::invalid(); }
}
