<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class ApplicationCatalogueQuery
{
    private function __construct(public ?string $category, public ?string $profile, public int $limit, public int $offset) {}

    /** @param array<string,mixed> $input */
    public static function fromArray(array $input): self
    {
        if (array_diff(array_keys($input), ['category', 'profile', 'limit', 'offset']) !== []) self::invalid();
        $limit = self::number(array_key_exists('limit', $input) ? $input['limit'] : 25);
        $offset = self::number(array_key_exists('offset', $input) ? $input['offset'] : 0);
        if (!in_array($limit, [25, 50, 100], true) || $offset > 2048) self::invalid();
        return new self(self::filter($input['category'] ?? null), self::filter($input['profile'] ?? null), $limit, $offset);
    }

    private static function number(mixed $value): int
    {
        if (is_int($value) && $value >= 0 && $value <= 2048) return $value;
        if (!is_string($value) || preg_match('/^(?:0|[1-9][0-9]{0,3})$/D', $value) !== 1) self::invalid();
        $number = (int) $value;
        if ($number > 2048) self::invalid();
        return $number;
    }

    private static function filter(mixed $value): ?string
    {
        if ($value === null) return null;
        if (!is_string($value) || $value === '' || strlen($value) > 256 || preg_match('//u', $value) !== 1
            || preg_match('/[\p{Cc}\p{Cf}]/u', $value) === 1) self::invalid();
        return $value;
    }

    private static function invalid(): never { throw new \InvalidArgumentException('Candidate catalogue query rejected.'); }
}
