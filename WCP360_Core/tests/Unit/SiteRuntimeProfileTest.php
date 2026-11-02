<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\SiteRuntimeProfile;

final class SiteRuntimeProfileTest extends TestCase
{
    /** @return array<string,mixed> */
    private static function row(): array
    {
        return ['application_server' => 'php_fpm', 'web_stack' => 'classic', 'stack_revision' => 1];
    }

    /** @return iterable<string,array{ApplicationServer,string}> */
    public static function existingEngines(): iterable
    {
        yield 'Classic FPM' => [ApplicationServer::PhpFpm, 'classic'];
        yield 'Advanced Worker' => [ApplicationServer::FrankenPhpWorker, 'advanced'];
    }

    #[DataProvider('existingEngines')]
    public function testExistingEnginesMapToTheirOriginalTopologyAndOnlyTwoStoredFields(ApplicationServer $server, string $stack): void
    {
        $profile = SiteRuntimeProfile::forServer($server);
        self::assertSame($server, $profile->server);
        self::assertSame($stack, $profile->stack);
        self::assertSame(1, $profile->revision);
        self::assertSame(['web_stack' => $stack, 'stack_revision' => 1], $profile->storage());
    }

    /** @return iterable<string,array{string,string,int|string}> */
    public static function databaseRows(): iterable
    {
        yield 'Classic native revision' => ['php_fpm', 'classic', 1];
        yield 'Classic string revision' => ['php_fpm', 'classic', '1'];
        yield 'Advanced native revision' => ['frankenphp_worker', 'advanced', 1];
        yield 'Advanced string revision' => ['frankenphp_worker', 'advanced', '1'];
    }

    #[DataProvider('databaseRows')]
    public function testStorageAcceptsOnlyTheCanonicalDbalRevisionForms(string $server, string $stack, int|string $revision): void
    {
        $profile = SiteRuntimeProfile::fromStorage([
            'application_server' => $server, 'web_stack' => $stack, 'stack_revision' => $revision,
        ]);
        self::assertSame(ApplicationServer::from($server), $profile->server);
        self::assertSame(['web_stack' => $stack, 'stack_revision' => 1], $profile->storage());
    }

    public function testProfileMatchingSurvivesRoundTripsButDoesNotConfuseAdvancedWithClassic(): void
    {
        $classic = SiteRuntimeProfile::forServer(ApplicationServer::PhpFpm);
        $advanced = SiteRuntimeProfile::forServer(ApplicationServer::FrankenPhpWorker);
        $copy = SiteRuntimeProfile::fromStorage($classic->storage() + ['application_server' => 'php_fpm']);
        self::assertNotSame($classic, $copy);
        self::assertTrue($classic->matches($copy));
        self::assertTrue($copy->matches($classic));
        self::assertTrue($advanced->matches(SiteRuntimeProfile::fromStorage($advanced->storage() + ['application_server' => 'frankenphp_worker'])));
        self::assertFalse($classic->matches($advanced));
        self::assertFalse($advanced->matches($classic));
    }

    public function testFullSiteRowsRetainOnlyTypedProfileMetadata(): void
    {
        $row = self::row() + [
            'id' => '019a3c3f-0000-7000-8000-000000000001', 'hostname' => 'synthetic.test',
            'tenant_id' => '019a3c3f-0000-7000-8000-000000000002', 'state' => 'PLAN_READY',
            'revision' => 2, 'worker_acknowledged' => 0, 'result' => 'private-runtime-input',
        ];
        $profile = SiteRuntimeProfile::fromStorage($row);
        self::assertSame(['web_stack' => 'classic', 'stack_revision' => 1], $profile->storage());
        self::assertSame(1, $profile->revision);
    }

    /** @return iterable<string,array{array<array-key,mixed>}> */
    public static function invalidStorage(): iterable
    {
        foreach (array_keys(self::row()) as $missing) {
            $row = self::row();
            unset($row[$missing]);
            yield 'missing '.$missing => [$row];
        }
        foreach (['', 'private-runtime-input', 'frankenphp_direct', 'PHP_FPM'] as $index => $server) {
            yield 'unknown engine '.$index => [array_replace(self::row(), ['application_server' => $server])];
        }
        foreach ([null, true, 1, ['private-runtime-input']] as $index => $server) {
            yield 'wrong engine type '.$index => [array_replace(self::row(), ['application_server' => $server])];
        }
        foreach (['', 'modern', 'legacy', 'private-runtime-input', 'Classic', 'advanced'] as $index => $stack) {
            yield 'unavailable or mismatched topology '.$index => [array_replace(self::row(), ['web_stack' => $stack])];
        }
        foreach ([null, true, 1, ['private-runtime-input']] as $index => $stack) {
            yield 'wrong topology type '.$index => [array_replace(self::row(), ['web_stack' => $stack])];
        }
        foreach ([null, false, true, 0, 2, -1, 1.0, '01', '+1', '1.0', '1 ', ' 1', [], 'private-runtime-input'] as $index => $revision) {
            yield 'noncanonical or unavailable revision '.$index => [array_replace(self::row(), ['stack_revision' => $revision])];
        }
        yield 'Worker cannot be Classic' => [['application_server' => 'frankenphp_worker', 'web_stack' => 'classic', 'stack_revision' => 1]];
    }

    /** @param array<array-key,mixed> $row */
    #[DataProvider('invalidStorage')]
    public function testMalformedUnsupportedAndMismatchedProfilesAreRejectedWithoutReflectingInput(array $row): void
    {
        try {
            SiteRuntimeProfile::fromStorage($row);
            self::fail('Malformed persisted runtime profile admitted.');
        } catch (\InvalidArgumentException $error) {
            self::assertSame('Candidate site runtime profile rejected.', $error->getMessage());
        }
    }
}
