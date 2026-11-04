<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Platform\LinuxRuntimeMetrics;

final class LinuxRuntimeMetricsTest extends TestCase
{
    /** @return iterable<string,array{?string,?int}> */
    public static function uptimeCases(): iterable
    {
        yield 'ordinary' => ["86400.25 129600.75\n", 86400];
        yield 'zero-is-known' => ["0.00 0.00\n", 0];
        yield 'fraction-truncated-not-rounded' => ["1.99 8.00\n", 1];
        yield 'idle-can-exceed-uptime' => ["10.00 1280.00\n", 10];
        yield 'leading-zeroes' => ["00012.01 00002.03\n", 12];
        yield 'horizontal-separator' => ["12.00\t 48.00\n", 12];
        yield 'maximum-integer' => [PHP_INT_MAX.".99 0.00\n", PHP_INT_MAX];
        yield 'missing' => [null, null];
        yield 'empty' => ['', null];
        yield 'truncated' => ["12.00 48.00", null];
        yield 'missing-idle' => ["12.00\n", null];
        yield 'negative' => ["-1.00 0.00\n", null];
        yield 'plus-sign' => ["+1.00 0.00\n", null];
        yield 'exponent' => ["1e3 0.00\n", null];
        yield 'nan' => ["NaN 0.00\n", null];
        yield 'infinity' => ["INF 0.00\n", null];
        yield 'comma-decimal' => ["1,00 0,00\n", null];
        yield 'extra-field' => ["1.00 0.00 7\n", null];
        yield 'extra-record' => ["1.00 0.00\n1.00 0.00\n", null];
        yield 'carriage-return' => ["1.00 0.00\r\n", null];
        yield 'null-byte' => ["1.00 0.00\x00\n", null];
        yield 'overflow' => [PHP_INT_MAX."0.00 0.00\n", null];
        yield 'idle-overflow' => ["1.00 ".PHP_INT_MAX."0.00\n", null];
        yield 'oversized' => [str_repeat('0', 4096)."1.00 0.00\n", null];
    }

    #[DataProvider('uptimeCases')]
    public function testUptimeIsBoundedAndUnknownIsNotZero(?string $content, ?int $expected): void
    {
        self::assertSame($expected, LinuxRuntimeMetrics::parseUptime($content));
    }

    /** @return iterable<string,array{?string,?array{one_minute:float,five_minutes:float,fifteen_minutes:float}}> */
    public static function loadCases(): iterable
    {
        $ordinary = ['one_minute' => 0.5, 'five_minutes' => 1.25, 'fifteen_minutes' => 2.75];
        yield 'ordinary' => ["0.50 1.25 2.75 2/100 1234\n", $ordinary];
        yield 'zero-is-known' => ["0.00 0.00 0.00 0/1 1\n", ['one_minute' => 0.0, 'five_minutes' => 0.0, 'fifteen_minutes' => 0.0]];
        yield 'horizontal-separator' => ["0.50\t1.25  2.75\t2/100 1234\n", $ordinary];
        yield 'sampled-counters-not-an-atomic-pair' => ["0.50 1.25 2.75 3/2 1234\n", $ordinary];
        yield 'leading-zeroes' => ["000.50 01.25 02.75 02/0100 01234\n", $ordinary];
        yield 'large-load-not-a-percentage' => ["1000.00 500.00 250.00 2/100 1234\n", ['one_minute' => 1000.0, 'five_minutes' => 500.0, 'fifteen_minutes' => 250.0]];
        yield 'missing' => [null, null];
        yield 'empty' => ['', null];
        yield 'truncated' => ["0.50 1.25 2.75 2/100 1234", null];
        yield 'missing-last-field' => ["0.50 1.25 2.75 2/100\n", null];
        yield 'negative-load' => ["-0.50 1.25 2.75 2/100 1234\n", null];
        yield 'nan' => ["NaN 1.25 2.75 2/100 1234\n", null];
        yield 'infinity' => ["INF 1.25 2.75 2/100 1234\n", null];
        yield 'exponent' => ["1e2 1.25 2.75 2/100 1234\n", null];
        yield 'locale-decimal' => ["0,50 1.25 2.75 2/100 1234\n", null];
        yield 'unexpected-precision' => ["0.500 1.25 2.75 2/100 1234\n", null];
        yield 'invalid-task-count' => ["0.50 1.25 2.75 2/x 1234\n", null];
        yield 'invalid-pid' => ["0.50 1.25 2.75 2/100 -1\n", null];
        yield 'extra-field' => ["0.50 1.25 2.75 2/100 1234 extra\n", null];
        yield 'extra-record' => ["0.50 1.25 2.75 2/100 1234\n\n", null];
        yield 'carriage-return' => ["0.50 1.25 2.75 2/100 1234\r\n", null];
        yield 'null-byte' => ["0.50 1.25 2.75 2/100 1234\x00\n", null];
        yield 'load-overflow' => [PHP_INT_MAX."0.00 1.25 2.75 2/100 1234\n", null];
        yield 'count-overflow' => ["0.50 1.25 2.75 2/".PHP_INT_MAX."0 1234\n", null];
        yield 'pid-overflow' => ["0.50 1.25 2.75 2/100 ".PHP_INT_MAX."0\n", null];
        yield 'oversized' => [str_repeat('0', 4096)."0.50 1.25 2.75 2/100 1234\n", null];
    }

    #[DataProvider('loadCases')]
    public function testLoadAverageValidatesWholeRecordWithoutPublishingTaskOrPidData(?string $content, ?array $expected): void
    {
        self::assertSame($expected, LinuxRuntimeMetrics::parseLoadAverage($content));
    }

    public function testEachUnavailableSourceLeavesTheOtherObservationAvailable(): void
    {
        $load = "0.50 1.25 2.75 2/100 1234\n";
        self::assertSame(['uptime_seconds' => null, 'load_average' => null], LinuxRuntimeMetrics::fromProcFiles(null, 'invalid'));
        self::assertSame(42, LinuxRuntimeMetrics::fromProcFiles("42.99 84.00\n", null)['uptime_seconds']);
        self::assertNull(LinuxRuntimeMetrics::fromProcFiles("42.99 84.00\n", null)['load_average']);
        self::assertNull(LinuxRuntimeMetrics::fromProcFiles('invalid', $load)['uptime_seconds']);
        self::assertSame(LinuxRuntimeMetrics::parseLoadAverage($load), LinuxRuntimeMetrics::fromProcFiles('invalid', $load)['load_average']);
    }

    public function testCollectorHasAnExactSmallJsonSafeShapeAndNoRequiredInput(): void
    {
        $result = LinuxRuntimeMetrics::collect();
        self::assertSame(['uptime_seconds', 'load_average'], array_keys($result));
        if ($result['uptime_seconds'] !== null) {
            self::assertGreaterThanOrEqual(0, $result['uptime_seconds']);
        }
        if ($result['load_average'] !== null) {
            self::assertSame(['one_minute', 'five_minutes', 'fifteen_minutes'], array_keys($result['load_average']));
            foreach ($result['load_average'] as $value) {
                self::assertIsFloat($value);
                self::assertTrue(is_finite($value));
                self::assertGreaterThanOrEqual(0.0, $value);
            }
        }
        self::assertIsString(json_encode($result, JSON_THROW_ON_ERROR));
        if (PHP_OS_FAMILY !== 'Linux') {
            self::assertSame(['uptime_seconds' => null, 'load_average' => null], $result);
        }
    }
}
