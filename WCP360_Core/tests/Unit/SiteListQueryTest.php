<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\Hosting\Domain\SiteListQuery;

final class SiteListQueryTest extends TestCase
{
    private const TENANT = 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa';

    public function testPreparationFiltersReusePageBoundsAndExcludeCursorsFromFilterChanges(): void
    {
        $query = SiteListQuery::fromArray(['tenant' => strtoupper(self::TENANT), 'q' => ' EXAMPLE.Test ',
            'state' => 'PLAN_READY', 'server' => 'frankenphp_worker', 'limit' => '50', 'after' => self::TENANT]);
        self::assertSame(self::TENANT, $query->tenant);
        self::assertSame('example.test', $query->search);
        self::assertSame(50, $query->limit);
        self::assertSame(self::TENANT, $query->after);
        self::assertSame(['tenant' => self::TENANT, 'state' => 'PLAN_READY', 'q' => 'example.test',
            'server' => 'frankenphp_worker', 'limit' => '50'], $query->filters());
        self::assertSame('', SiteListQuery::fromArray(['tenant' => self::TENANT])->server);
    }

    public static function invalid(): iterable
    {
        foreach ([['state' => 'QUEUED'], ['state' => ['PLAN_READY']], ['server' => 'apache'],
            ['server' => ['php_fpm']], ['actor_id' => self::TENANT], ['q' => '%'],
            ['after' => self::TENANT, 'before' => self::TENANT], ['limit' => '10000']] as $patch) {
            yield [array_replace(['tenant' => self::TENANT], $patch)];
        }
    }

    #[DataProvider('invalid')]
    public function testOwnerFiltersAndSharedBoundsRejectInvalidInput(array $input): void
    {
        $this->expectException(BadRequestHttpException::class);
        SiteListQuery::fromArray($input);
    }
}
