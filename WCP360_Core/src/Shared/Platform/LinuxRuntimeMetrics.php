<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Read-only procfs observations in the current process namespace, not CPU usage
 * percentages, cgroup capacity or health verdicts. Compatible with PHP 8.1.
 *
 * @phpstan-type LoadAverage array{one_minute:float,five_minutes:float,fifteen_minutes:float}
 * @phpstan-type RuntimeMetrics array{uptime_seconds:?int,load_average:?LoadAverage}
 */
final class LinuxRuntimeMetrics
{
    private const MAX_BYTES = 4096;

    /** @return RuntimeMetrics */
    public static function collect(): array
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return self::fromProcFiles(null, null);
        }

        // Fixed local paths only; no caller selectors, subprocess or network.
        return self::fromProcFiles(self::readBounded('/proc/uptime'), self::readBounded('/proc/loadavg'));
    }

    /** @return RuntimeMetrics */
    public static function fromProcFiles(?string $uptime, ?string $loadAverage): array
    {
        // An unavailable observation must not hide a different valid observation.
        return ['uptime_seconds' => self::parseUptime($uptime), 'load_average' => self::parseLoadAverage($loadAverage)];
    }

    public static function parseUptime(?string $content): ?int
    {
        if ($content === null || strlen($content) > self::MAX_BYTES
            || preg_match('/\A([0-9]+)\.[0-9]{2}[ \t]+([0-9]+)\.[0-9]{2}\n\z/D', $content, $parts) !== 1) {
            return null;
        }
        // Aggregate idle time can exceed uptime on multicore hosts. Do not
        // infer an invalid observation or a CPU percentage from that relation.
        $seconds = self::integer($parts[1]);
        if ($seconds === null || self::integer($parts[2]) === null) {
            return null;
        }

        return $seconds;
    }

    /** @return LoadAverage|null */
    public static function parseLoadAverage(?string $content): ?array
    {
        if ($content === null || strlen($content) > self::MAX_BYTES
            || preg_match('/\A([0-9]+\.[0-9]{2})[ \t]+([0-9]+\.[0-9]{2})[ \t]+([0-9]+\.[0-9]{2})[ \t]+([0-9]+)\/([0-9]+)[ \t]+([0-9]+)\n\z/D', $content, $parts) !== 1) {
            return null;
        }
        // Validate the entire record, but never publish task counts or a PID.
        // Counts are sampled separately by Linux; do not assume an atomic pair.
        foreach ([4, 5, 6] as $index) {
            if (self::integer($parts[$index]) === null) {
                return null;
            }
        }
        $values = [];
        foreach ([1, 2, 3] as $index) {
            $whole = explode('.', $parts[$index], 2)[0];
            $value = (float) $parts[$index];
            if (self::integer($whole) === null || !is_finite($value)) {
                return null;
            }
            $values[] = $value;
        }

        return ['one_minute' => $values[0], 'five_minutes' => $values[1], 'fifteen_minutes' => $values[2]];
    }

    /** Input is already restricted to ASCII decimal digits by the record parser. */
    private static function integer(string $digits): ?int
    {
        $digits = ltrim($digits, '0');
        $maximum = (string) PHP_INT_MAX;
        if (strlen($digits) > strlen($maximum)
            || (strlen($digits) === strlen($maximum) && strcmp($digits, $maximum) > 0)) {
            return null;
        }

        return (int) $digits;
    }

    private static function readBounded(string $path): ?string
    {
        $stream = null;
        try {
            $stream = @fopen($path, 'rb');
            if (!is_resource($stream)) {
                return null;
            }
            $content = @stream_get_contents($stream, self::MAX_BYTES + 1);

            return is_string($content) && strlen($content) <= self::MAX_BYTES && feof($stream) ? $content : null;
        } catch (\Throwable) {
            // Read failures are unknown observations, never raw host diagnostics.
            return null;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }
}
