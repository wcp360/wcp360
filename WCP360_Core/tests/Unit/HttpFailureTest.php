<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wcp360\Core\Shared\Http\HttpFailure;
use Wcp360\Core\Shared\Http\IdempotencyConflict;

final class HttpFailureTest extends TestCase
{
    #[DataProvider('statuses')]
    public function testStatusesUseFixedCodesWithoutCopyingExceptionDetails(int $status, string $code): void
    {
        $failure = HttpFailure::fromException(new HttpException($status, 'private-password /srv/tenant/secret sql'));
        self::assertSame($status, $failure->status);
        self::assertSame($code, $failure->code);
        self::assertStringNotContainsString('private-password', serialize($failure));
        self::assertStringNotContainsString('/srv/tenant', serialize($failure));
    }

    public static function statuses(): iterable
    {
        yield [400, 'VALIDATION_FAILED'];
        yield [401, 'AUTHENTICATION_REQUIRED'];
        yield [403, 'AUTHORIZATION_DENIED'];
        yield [404, 'RESOURCE_NOT_FOUND'];
        yield [405, 'VALIDATION_FAILED'];
        yield [409, 'RESOURCE_CONFLICT'];
        yield [429, 'RATE_LIMITED'];
        yield [500, 'INTERNAL_ERROR'];
        yield [503, 'SERVICE_UNAVAILABLE'];
        yield [504, 'OPERATION_TIMEOUT'];
    }

    public function testIdempotencyHasATypedDistinctionInsteadOfMessageParsing(): void
    {
        self::assertSame('IDEMPOTENCY_CONFLICT', HttpFailure::fromException(new IdempotencyConflict())->code);
        self::assertSame('RESOURCE_CONFLICT', HttpFailure::fromException(new HttpException(409, 'IDEMPOTENCY_CONFLICT'))->code);
        self::assertSame(500, HttpFailure::fromStatus(200)->status);
        self::assertSame('INTERNAL_ERROR', HttpFailure::fromException(new \RuntimeException('private-driver-message'))->code);
    }
}
