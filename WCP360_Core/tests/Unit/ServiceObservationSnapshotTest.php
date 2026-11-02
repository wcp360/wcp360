<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Module\Platform\Domain\ServiceUnitCatalogue;

final class ServiceObservationSnapshotTest extends TestCase
{
    private static function block(string $id = 'nginx.service', array $changes = []): string
    {
        $properties = array_replace(['Id' => $id, 'LoadState' => 'loaded', 'ActiveState' => 'active',
            'SubState' => 'running', 'UnitFileState' => 'enabled'], $changes);
        $lines = [];
        foreach ($properties as $key => $value) $lines[] = $key.'='.$value;
        return implode("\n", $lines);
    }

    private static function names(): array
    {
        return ['nginx.service','apache2.service','php8.1-fpm.service','php8.2-fpm.service','php8.3-fpm.service',
            'php8.4-fpm.service','php8.5-fpm.service','caddy.service','frankenphp.service','postgresql.service',
            'mysql.service','mariadb.service','redis-server.service','docker.service'];
    }

    public function testCatalogueHasNineFixedFamiliesAndFourteenUniqueReferencesWithoutControlMetadata(): void
    {
        $entries = ServiceUnitCatalogue::entries();
        self::assertSame(['nginx','apache','php_fpm','caddy','frankenphp','postgresql','mysql','redis','docker'], array_column($entries, 'id'));
        self::assertSame(['Nginx','Apache','PHP-FPM','Caddy','FrankenPHP','PostgreSQL','MySQL/MariaDB','Redis','Docker'], array_column($entries, 'label'));
        self::assertSame(['Hosting','Hosting','Hosting','Hosting','Hosting','Data','Data','Operations','Platform'], array_column($entries, 'owner'));
        self::assertSame(self::names(), ServiceUnitCatalogue::unitNames());
        self::assertSame(self::names(), array_values(array_unique(ServiceUnitCatalogue::unitNames())));
        self::assertSame(['mysql.service','mariadb.service'], $entries[6]['units']);
        foreach ($entries as $entry) self::assertSame(['id','label','owner','units'], array_keys($entry));
    }

    public function testAllFourteenFixedUnitsCanBeObservedInSourceOrderWithoutInventingFields(): void
    {
        $raw = implode("\n\n", array_map(self::block(...), self::names()))."\n";
        $snapshot = ServiceObservationSnapshot::fromSystemdShow($raw);
        self::assertSame(['status','units'], array_keys(get_object_vars($snapshot)));
        self::assertSame('observed', $snapshot->status);
        self::assertSame(self::names(), array_column($snapshot->units, 'id'));
        foreach ($snapshot->units as $unit) {
            self::assertSame(['id','load_state','active_state','sub_state','unit_file_state'], array_keys($unit));
            self::assertSame('loaded', $unit['load_state']);
            self::assertSame('active', $unit['active_state']);
            self::assertSame('running', $unit['sub_state']);
            self::assertSame('enabled', $unit['unit_file_state']);
        }
    }

    public function testPropertyOrderIsFreeAndUnknownStatesRemainOpaqueWithEmptyFileStateNull(): void
    {
        $raw = "UnitFileState=\nSubState=future_mode-9\nId=postgresql.service\nActiveState=UNKNOWN_NEXT\nLoadState=future-state\n";
        $snapshot = ServiceObservationSnapshot::fromSystemdShow($raw);
        self::assertSame([['id' => 'postgresql.service', 'load_state' => 'future-state', 'active_state' => 'UNKNOWN_NEXT',
            'sub_state' => 'future_mode-9', 'unit_file_state' => null]], $snapshot->units);
    }

    public function testNotFoundIsPreservedAsAnObservationAndMissingUnitsAreNeverSynthesized(): void
    {
        $snapshot = ServiceObservationSnapshot::fromSystemdShow(self::block('apache2.service', [
            'LoadState' => 'not-found', 'ActiveState' => 'inactive', 'SubState' => 'dead', 'UnitFileState' => '']));
        self::assertSame('observed', $snapshot->status);
        self::assertSame([['id' => 'apache2.service', 'load_state' => 'not-found', 'active_state' => 'inactive',
            'sub_state' => 'dead', 'unit_file_state' => null]], $snapshot->units);
    }

    public function testIdenticalCanonicalAliasRecordsCollapseAndRetainFirstObservationOrder(): void
    {
        $maria = self::block('mariadb.service');
        $raw = self::block()."\n\n".$maria."\n\n".self::block('caddy.service')."\n\n".$maria."\n";
        $snapshot = ServiceObservationSnapshot::fromSystemdShow($raw);
        self::assertSame(['nginx.service','mariadb.service','caddy.service'], array_column($snapshot->units, 'id'));
        self::assertCount(3, $snapshot->units);
    }

    public static function conflictingAliasProperties(): iterable
    {
        foreach (['LoadState','ActiveState','SubState','UnitFileState'] as $property) yield $property => [$property];
    }

    #[DataProvider('conflictingAliasProperties')]
    public function testConflictingCanonicalAliasesAreRejectedInsteadOfOverwritingAnObservation(string $property): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ServiceObservationSnapshot::fromSystemdShow(self::block('mariadb.service')."\n\n".self::block('mariadb.service', [$property => 'different_state']));
    }

    public function testUnavailableFactoriesCarryNoStaleUnitsOrNativeOutput(): void
    {
        foreach (['not_configured' => ServiceObservationSnapshot::notConfigured(), 'failed' => ServiceObservationSnapshot::failed()] as $status => $snapshot) {
            self::assertSame(['status','units'], array_keys(get_object_vars($snapshot)));
            self::assertSame($status, $snapshot->status);
            self::assertSame([], $snapshot->units);
        }
    }

    public function testRecordLimitIsAppliedBeforeAliasDeduplication(): void
    {
        $fourteen = implode("\n\n", array_fill(0, 14, self::block('mariadb.service')));
        self::assertCount(1, ServiceObservationSnapshot::fromSystemdShow($fourteen)->units);
        $this->expectException(\InvalidArgumentException::class);
        ServiceObservationSnapshot::fromSystemdShow($fourteen."\n\n".self::block('mariadb.service'));
    }

    public function testRawByteBudgetAcceptsItsBoundaryAndRejectsOneByteMore(): void
    {
        $block = self::block();
        $raw = $block.str_repeat("\n", 32768 - strlen($block));
        self::assertSame(32768, strlen($raw));
        self::assertCount(1, ServiceObservationSnapshot::fromSystemdShow($raw)->units);
        $this->expectException(\InvalidArgumentException::class);
        ServiceObservationSnapshot::fromSystemdShow($raw."\n");
    }

    public function testStateWordBudgetIsAppliedToEveryStateWithoutRequiringKnownEnums(): void
    {
        $word = str_repeat('x', 64);
        $snapshot = ServiceObservationSnapshot::fromSystemdShow(self::block('docker.service', [
            'LoadState' => $word, 'ActiveState' => $word, 'SubState' => $word, 'UnitFileState' => $word]));
        self::assertSame($word, $snapshot->units[0]['sub_state']);
        self::assertSame($word, $snapshot->units[0]['unit_file_state']);
    }

    public static function invalidOutputs(): iterable
    {
        $base = self::block();
        yield 'empty' => [''];
        yield 'only LF' => ["\n\n"];
        yield 'only spaces' => ['  '];
        yield 'only whitespace' => [" \n \n"];
        yield 'unknown property' => [str_replace('SubState=running', 'Environment=private-native-value', $base)];
        yield 'extra property' => [$base."\nExecStart=private-native-value"];
        yield 'duplicate property' => [str_replace('SubState=running', 'ActiveState=active', $base)];
        yield 'missing property' => [str_replace("\nSubState=running", '', $base)];
        yield 'missing equals' => [str_replace('SubState=running', 'SubState', $base)];
        yield 'wrong property case' => [str_replace('Id=', 'id=', $base)];
        yield 'space in property name' => [str_replace('LoadState=', 'LoadState =', $base)];
        yield 'two equals' => [str_replace('SubState=running', 'SubState=running=private', $base)];
        yield 'unknown canonical alias' => [self::block('private-custom.service')];
        yield 'unknown PHP version' => [self::block('php8.6-fpm.service')];
        yield 'path as unit' => [self::block('/etc/private/nginx.service')];
        yield 'argument as unit' => [self::block('--root=/private')];
        yield 'command fragment as unit' => [self::block('nginx.service;private-command')];
        yield 'case changed unit' => [self::block('NGINX.service')];
        yield 'empty Id' => [self::block('')];
        yield 'space in state' => [self::block('nginx.service', ['ActiveState' => 'private state'])];
        yield 'non ASCII word' => [self::block('nginx.service', ['SubState' => 'privé'])];
        yield 'HTML state' => [self::block('nginx.service', ['SubState' => '<private>'])];
        yield 'UTF8 BOM' => ["\xef\xbb\xbf".$base];
        yield 'invalid UTF8' => [$base."\xff"];
        yield 'CRLF output' => [str_replace("\n", "\r\n", $base)];
        foreach (["\0","\x01","\x07","\x08","\t","\x0b","\x0c","\r","\x1b","\x1f","\x7f"] as $index => $control) {
            yield 'control '.$index => [self::block('nginx.service', ['SubState' => 'private'.$control.'value'])];
        }
        foreach (['LoadState','ActiveState','SubState','UnitFileState'] as $property) {
            yield 'oversized '.$property => [self::block('nginx.service', [$property => str_repeat('x', 65)])];
            if ($property !== 'UnitFileState') yield 'empty '.$property => [self::block('nginx.service', [$property => ''])];
        }
    }

    #[DataProvider('invalidOutputs')]
    public function testMalformedPrivateOrUnboundedOutputIsRejectedWithAGenericError(string $raw): void
    {
        try {
            ServiceObservationSnapshot::fromSystemdShow($raw);
            self::fail('Malformed service output was admitted.');
        } catch (\InvalidArgumentException $error) {
            self::assertSame('Service observation rejected.', $error->getMessage());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('private', (string) $error);
        }
    }

    public function testReturnedArraysCannotAlterAnExistingSnapshot(): void
    {
        $snapshot = ServiceObservationSnapshot::fromSystemdShow(self::block());
        $copy = $snapshot->units;
        $copy[0]['active_state'] = 'changed';
        $copy[] = ['id' => 'private'];
        self::assertSame('active', $snapshot->units[0]['active_state']);
        self::assertCount(1, $snapshot->units);
    }
}
