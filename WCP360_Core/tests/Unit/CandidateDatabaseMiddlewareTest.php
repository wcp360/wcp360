<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Security\CandidateDatabaseMiddleware;
use Wcp360\Core\Shared\Security\CandidateDatabaseUrl;
use Wcp360\Core\Shared\Security\CandidateSessionGrants;

final class CandidateDatabaseMiddlewareTest extends TestCase
{
    public function testSharedProfileLeavesTheExistingDriverUntouched(): void
    {
        $driver = $this->createMock(Driver::class);
        $driver->expects(self::never())->method('connect');
        self::assertSame($driver, (new CandidateDatabaseMiddleware('shared', 'user'))->wrap($driver));
    }

    #[DataProvider('invalidProfiles')]
    public function testInvalidProfileFailsBeforeConnecting(string $mode, string $context): void
    {
        $driver = $this->createMock(Driver::class);
        $driver->expects(self::never())->method('connect');
        $this->expectExceptionMessage(CandidateDatabaseUrl::REJECTED);
        (new CandidateDatabaseMiddleware($mode, $context))->wrap($driver);
    }

    public static function invalidProfiles(): iterable
    {
        yield ['session-isolated', 'root'];
        yield ['unknown', 'user'];
        yield ['', 'admin'];
    }

    public function testIsolatedDriverFailureDropsPrivateExceptionChain(): void
    {
        $driver = $this->createMock(Driver::class);
        $driver->expects(self::once())->method('connect')->willThrowException(new \RuntimeException('postgresql://private:secret@private-host/db'));
        try {
            (new CandidateDatabaseMiddleware('session-isolated', 'user'))->wrap($driver)->connect(['driver' => 'pdo_pgsql']);
            self::fail('Connection failure was ignored.');
        } catch (\RuntimeException $error) {
            self::assertSame(CandidateDatabaseUrl::REJECTED, $error->getMessage());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('private-host', (string) $error);
        }
    }

    public function testIsolatedModeRefusesNonPostgresDriverBeforeConnection(): void
    {
        $driver = $this->createMock(Driver::class);
        $driver->expects(self::never())->method('connect');
        $this->expectExceptionMessage(CandidateDatabaseUrl::REJECTED);
        (new CandidateDatabaseMiddleware('session-isolated', 'user'))->wrap($driver)->connect(['driver' => 'pdo_sqlite']);
    }

    #[DataProvider('observations')]
    public function testGateAcceptsOnlyCompleteMatchingObservations(string $context, array|false $row, bool $allowed): void
    {
        $result = $this->createStub(Result::class);
        $result->method('fetchAssociative')->willReturn($row);
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('exec')->with('SET search_path TO pg_catalog, public')->willReturn(0);
        $connection->expects(self::once())->method('query')->willReturn($result);
        if (!$allowed) {
            $this->expectExceptionMessage(CandidateDatabaseUrl::REJECTED);
        }
        CandidateSessionGrants::verify($connection, $context);
        if ($allowed) {
            self::assertTrue(true);
        }
    }

    public static function observations(): iterable
    {
        $user = ['identity_ok' => true, 'user_crud' => true, 'admin_crud' => false, 'user_access' => true, 'admin_access' => false];
        yield 'user' => ['user', $user, true];
        yield 'admin' => ['admin', array_replace($user, ['user_crud' => false, 'admin_crud' => true, 'user_access' => false, 'admin_access' => true]), true];
        yield 'worker' => ['worker', array_replace($user, ['user_crud' => false, 'user_access' => false]), true];
        yield 'wrong-surface' => ['admin', $user, false];
        yield 'worker-session-access' => ['worker', $user, false];
        yield 'empty' => ['user', false, false];
        foreach (['identity_ok', 'user_crud', 'admin_access'] as $field) {
            $badValue = $field === 'admin_access' ? true : false;
            yield $field => ['user', array_replace($user, [$field => $badValue]), false];
            yield 'missing-'.$field => ['user', array_diff_key($user, [$field => true]), false];
            yield 'null-'.$field => ['user', array_replace($user, [$field => null]), false];
        }
        yield 'postgres-text-booleans' => ['user', ['identity_ok' => 't', 'user_crud' => 't', 'admin_access' => 'f'], true];
        yield 'unknown-boolean' => ['user', array_replace($user, ['admin_access' => 'false']), false];
    }
}
