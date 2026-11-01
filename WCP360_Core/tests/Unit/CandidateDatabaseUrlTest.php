<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Security\CandidateDatabaseUrl;

final class CandidateDatabaseUrlTest extends TestCase
{
    public function testSharedDevelopmentProfileRetainsResolvedSqliteConfiguration(): void
    {
        $requested = [];
        $url = (new CandidateDatabaseUrl())->getEnv('wcp_database', 'DATABASE_URL', function (string $name) use (&$requested): string {
            $requested[] = $name;
            return match ($name) {
                'WCP_DATABASE_MODE' => 'shared',
                'resolve:DATABASE_URL' => 'sqlite:////private/workbench.db',
                default => throw new \RuntimeException('Unexpected secret requested.'),
            };
        });
        self::assertSame('sqlite:////private/workbench.db', $url);
        self::assertSame(['WCP_DATABASE_MODE', 'resolve:DATABASE_URL'], $requested);
    }

    #[DataProvider('contexts')]
    public function testIsolatedSelectionReadsOnlyItsOwnUnresolvedCredential(string $context): void
    {
        $requested = [];
        $url = 'postgresql://candidate:private%25password@localhost:5432/wcp360_test?serverVersion=17&charset=utf8&sslmode=verify-full';
        self::assertSame($url, (new CandidateDatabaseUrl())->getEnv('wcp_database', 'DATABASE_URL', function (string $name) use ($context, $url, &$requested): string {
            $requested[] = $name;
            return match ($name) {
                'WCP_DATABASE_MODE' => 'session-isolated',
                'WCP_CONTEXT' => $context,
                'WCP_DATABASE_'.strtoupper($context).'_URL' => $url,
                default => throw new \RuntimeException('Another context secret was requested.'),
            };
        }));
        self::assertSame(['WCP_DATABASE_MODE', 'WCP_CONTEXT', 'WCP_DATABASE_'.strtoupper($context).'_URL'], $requested);
    }

    public static function contexts(): iterable
    {
        foreach (['user', 'admin', 'worker', 'setup'] as $context) {
            yield $context => [$context];
        }
    }

    #[DataProvider('invalidUrls')]
    public function testIsolatedInvalidUrlsNeverFallbackOrExposeConfiguration(string $url): void
    {
        $this->expectExceptionMessage(CandidateDatabaseUrl::REJECTED);
        (new CandidateDatabaseUrl())->getEnv('wcp_database', 'DATABASE_URL', fn (string $name): string => match ($name) {
            'WCP_DATABASE_MODE' => 'session-isolated',
            'WCP_CONTEXT' => 'user',
            'WCP_DATABASE_USER_URL' => $url,
            default => throw new \LogicException('The shared credential must not be read.'),
        });
    }

    public static function invalidUrls(): iterable
    {
        $base = 'postgresql://user:secret@localhost/wcp360_test';
        foreach ([
            '', 'sqlite:///:memory:', 'mysql://user:secret@localhost/db',
            'postgresql://localhost/db', 'postgresql://user@localhost/db',
            'postgresql://user:@localhost/db', 'postgresql://user:secret@localhost/',
            $base.'#secret', $base."\n", $base.'?options=-c%20role%3Dpostgres',
            $base.'?serverVersion=16', $base.'?serverVersion=17&serverVersion=17',
            $base.'?charset=utf8&%63harset=utf8', $base.'?sslmode=prefer',
            $base.'?sslmode=verify-full&', $base.'?unknown=secret', $base.'?charset=utf8%ZZ',
            'postgresql://user:'.str_repeat('a', 8192).'@localhost/db',
        ] as $index => $url) {
            yield 'invalid-'.$index => [$url];
        }
    }

    public function testMissingCredentialAndUnknownModeOrContextFailWithoutExceptionChains(): void
    {
        foreach ([
            ['mode' => 'session-isolated', 'context' => 'user'],
            ['mode' => 'session-isolated', 'context' => 'USER'],
            ['mode' => 'session-isolated', 'context' => 'root'],
            ['mode' => '', 'context' => 'user'],
            ['mode' => 'isolated', 'context' => 'user'],
        ] as $case) {
            try {
                (new CandidateDatabaseUrl())->getEnv('wcp_database', 'DATABASE_URL', fn (string $name): string => match ($name) {
                    'WCP_DATABASE_MODE' => $case['mode'],
                    'WCP_CONTEXT' => $case['context'],
                    default => throw new \RuntimeException('private-password-in-provider-error'),
                });
                self::fail('Invalid profile was accepted.');
            } catch (\RuntimeException $error) {
                self::assertSame(CandidateDatabaseUrl::REJECTED, $error->getMessage());
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-password', (string) $error);
            }
        }
    }
}
