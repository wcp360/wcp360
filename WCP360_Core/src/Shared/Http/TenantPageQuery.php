<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

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
        if (array_diff(array_keys($input), ['tenant', 'limit', 'after', 'before', ...$extraFields])) self::invalid();
        foreach ($input as $value) if (!is_string($value) || strlen($value) > 253) self::invalid();
        $tenant = self::uuid($input['tenant'] ?? '');
        $limit = $input['limit'] ?? '25';
        $after = ($input['after'] ?? '') !== '' ? self::uuid($input['after']) : null;
        $before = ($input['before'] ?? '') !== '' ? self::uuid($input['before']) : null;
        if (!in_array($limit, ['25', '50', '100'], true) || ($after !== null && $before !== null)) self::invalid();

        return new self($tenant, (int) $limit, $after, $before);
    }

    public static function hostnameFragment(string $value): string
    {
        if (strlen($value) > 253) self::invalid();
        $search = strtolower(trim($value));
        if (preg_match('/\A[a-z0-9.-]{0,253}\z/D', $search) !== 1) self::invalid();

        return $search;
    }

    private static function uuid(string $value): string
    {
        if (strlen($value) !== 36 || !Uuid::isValid($value)) self::invalid();

        return Uuid::fromString($value)->toRfc4122();
    }

    public static function invalid(): never
    {
        throw new BadRequestHttpException('Invalid list filters or cursor.');
    }
}
