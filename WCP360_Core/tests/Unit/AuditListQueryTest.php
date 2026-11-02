<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\Observability\Domain\AuditListQuery;

final class AuditListQueryTest extends TestCase
{
    private const ID = 'abcdefab-cdef-7000-8000-abcdefabcdef';

    public function testDefaultAndCanonicalFiltersDoNotRetainCursors(): void
    {
        $query = AuditListQuery::fromArray([]);
        self::assertSame(25, $query->limit);
        self::assertNull($query->after);
        self::assertNull($query->before);
        $query = AuditListQuery::fromArray(['actor' => strtoupper(self::ID), 'tenant' => self::ID,
            'resource' => self::ID, 'request' => str_repeat('a', 32), 'kind' => 'hosting.plan.succeeded',
            'limit' => '50', 'after' => self::ID, 'from' => '2024-02-29T00:00:00Z', 'until' => '2026-11-02T23:59:59Z']);
        self::assertSame(50, $query->limit);
        self::assertSame(self::ID, $query->filters['actor']);
        self::assertSame(self::ID, $query->after);
        self::assertArrayNotHasKey('after', $query->filters);
        self::assertArrayNotHasKey('before', $query->filters);
    }

    public static function invalid(): iterable
    {
        yield 'unknown column' => [['password_hash' => 'private-input']];
        yield 'caller SQL' => [['sort' => 'id DESC']];
        yield 'array' => [['kind' => ['hosting.plan.queued']]];
        yield 'nonstring' => [['limit' => 25]];
        yield 'unbounded' => [['kind' => str_repeat('a', 254)]];
        yield 'large page' => [['limit' => '1000']];
        yield 'zero' => [['limit' => '0']];
        yield 'both directions' => [['after' => self::ID, 'before' => self::ID]];
        yield 'bad cursor' => [['after' => 'private-id']];
        yield 'bad actor' => [['actor' => '../private']];
        yield 'bad tenant' => [['tenant' => 'private-id']];
        yield 'bad resource' => [['resource' => 'private-id']];
        yield 'wildcard' => [['kind' => '%']];
        yield 'injection' => [['kind' => "x' OR 1=1 --"]];
        yield 'uppercase kind' => [['kind' => 'HOSTING']];
        yield 'long kind' => [['kind' => str_repeat('a', 65)]];
        yield 'request' => [['request' => str_repeat('A', 32)]];
        yield 'short request' => [['request' => 'a']];
        yield 'invalid day' => [['from' => '2026-02-30T00:00:00Z']];
        yield 'invalid leap' => [['from' => '2025-02-29T00:00:00Z']];
        yield 'invalid time' => [['until' => '2026-11-02T24:00:00Z']];
        yield 'offset' => [['from' => '2026-11-02T00:00:00+00:00']];
        yield 'date only' => [['from' => '2026-11-02']];
        yield 'NUL date' => [['from' => "2026-11-02T00:00:00Z\0"]];
        yield 'reversed range' => [['from' => '2026-11-02T00:00:00Z', 'until' => '2026-11-01T00:00:00Z']];
    }

    #[DataProvider('invalid')]
    public function testUnsupportedFiltersAreRejectedWithoutReflectingTheirValues(array $input): void
    {
        try { AuditListQuery::fromArray($input); self::fail('Unsupported filters admitted.'); }
        catch (BadRequestHttpException $error) { self::assertSame('Invalid list filters or cursor.', $error->getMessage()); }
    }
}
