<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

/** Shared transport limits; cursors and filters never grant resource authority. */
#[Exclude]
final readonly class CursorPageQuery
{
    private function __construct(public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input
     * @param list<string> $extraFields Fixed names from the owning query, never request-supplied SQL.
     */
    public static function fromArray(array $input, array $extraFields): self
    {
        if (array_diff(array_keys($input), ['limit', 'after', 'before', ...$extraFields])) self::invalid();
        foreach ($input as $value) if (!is_string($value) || strlen($value) > 253) self::invalid();
        $limit = $input['limit'] ?? '25';
        $after = ($input['after'] ?? '') !== '' ? self::uuid($input['after']) : null;
        $before = ($input['before'] ?? '') !== '' ? self::uuid($input['before']) : null;
        if (!in_array($limit, ['25', '50', '100'], true) || ($after !== null && $before !== null)) self::invalid();

        return new self((int) $limit, $after, $before);
    }

    public static function uuid(string $value): string
    {
        if (strlen($value) !== 36 || !Uuid::isValid($value)) self::invalid();

        return Uuid::fromString($value)->toRfc4122();
    }

    public static function invalid(): never
    {
        throw new BadRequestHttpException('Invalid list filters or cursor.');
    }
}
