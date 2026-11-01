<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\Operations\Domain\JobListQuery;

final class JobListQueryTest extends TestCase
{
    private const TENANT = 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa';

    public function testCanonicalIdsBoundedSizesAndSearchNormalization(): void
    {
        $query = JobListQuery::fromArray(['tenant' => strtoupper(self::TENANT), 'q' => '  EXAMPLE.Test ', 'state' => 'QUEUED', 'limit' => '50']);
        self::assertSame(self::TENANT, $query->tenant);
        self::assertSame('example.test', $query->search);
        self::assertSame(50, $query->limit);
        self::assertNull($query->after);
        self::assertNull($query->before);
        self::assertSame(['tenant' => self::TENANT, 'state' => 'QUEUED', 'q' => 'example.test', 'limit' => '50'], $query->filters());
        self::assertSame(25, JobListQuery::fromArray(['tenant' => self::TENANT])->limit);
    }

    #[DataProvider('invalid')]
    public function testMalformedAndUnboundedInputFailsBeforeAnyDatabaseRead(array $input): void
    {
        $this->expectException(BadRequestHttpException::class);
        JobListQuery::fromArray($input);
    }

    public static function invalid(): iterable
    {
        yield 'missing tenant' => [[]];
        foreach ([
            ['tenant' => 'not-a-uuid'], ['tenant' => ['other']], ['limit' => 25], ['limit' => '1000'],
            ['limit' => '0'], ['limit' => '025'], ['state' => 'CANCELLED'], ['state' => ['QUEUED']],
            ['q' => '%'], ['q' => '_'], ['q' => '<script>'], ['q' => str_repeat('a', 254)],
            ['after' => 'private-cursor-marker'], ['before' => ['bad']], ['after' => self::TENANT, 'before' => self::TENANT],
            ['order' => 'hostname'], ['actor_id' => self::TENANT],
        ] as $index => $patch) {
            yield 'refusal '.$index => [array_replace(['tenant' => self::TENANT], $patch)];
        }
    }
}
