<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Correlation\RequestId;

final class RequestIdTest extends TestCase
{
    public function testFreshReferencesHaveTheBoundedFormatAndLegacyNullIsAccepted(): void
    {
        $first = RequestId::generate();
        self::assertTrue(RequestId::valid($first));
        self::assertNotSame($first, RequestId::generate());
        self::assertFalse(RequestId::valid(null));
        RequestId::assertValid(null);
        RequestId::assertValid($first);
    }

    public static function invalidReferences(): iterable
    {
        foreach (['', str_repeat('a', 31), str_repeat('a', 33), str_repeat('A', 32), str_repeat('g', 32),
            str_repeat('b', 32)."\n", '<script>request-marker</script>'] as $value) {
            yield [$value];
        }
    }

    #[DataProvider('invalidReferences')]
    public function testInvalidInternalReferenceIsRejected(string $value): void
    {
        self::assertFalse(RequestId::valid($value));
        $this->expectException(\InvalidArgumentException::class);
        RequestId::assertValid($value);
    }
}
