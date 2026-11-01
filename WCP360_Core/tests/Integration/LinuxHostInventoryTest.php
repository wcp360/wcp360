<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Platform\LinuxHostInventory;

final class LinuxHostInventoryTest extends TestCase
{
    public function testRealLinuxIpv4TcpAndUdpBindingsAreObservedWithoutProcessOrAddressDisclosure(): void
    {
        $this->requireLinux();
        $tcp = stream_socket_server('tcp://127.0.0.1:0', $tcpError, $tcpMessage);
        $udp = stream_socket_server('udp://127.0.0.1:0', $udpError, $udpMessage, STREAM_SERVER_BIND);
        self::assertIsResource($tcp, 'The disposable Linux test profile must allow local TCP binding.');
        self::assertIsResource($udp, 'The disposable Linux test profile must allow local UDP binding.');
        try {
            $tcpPort = $this->port($tcp);
            $udpPort = $this->port($udp);
            $report = LinuxHostInventory::collect();
            self::assertNotNull($report['network']['tcp4'], 'Unreadable or malformed live TCP data must not silently pass.');
            self::assertNotNull($report['network']['udp4'], 'Unreadable or malformed live UDP data must not silently pass.');
            self::assertContains($tcpPort, $report['network']['tcp4']);
            self::assertContains($udpPort, $report['network']['udp4']);
            self::assertNotNull($report['memory']);
            self::assertGreaterThan(0, $report['memory']['total_bytes']);
            self::assertGreaterThanOrEqual(0, $report['memory']['available_bytes']);
            self::assertLessThanOrEqual($report['memory']['total_bytes'], $report['memory']['available_bytes']);
            self::assertNotNull($report['disk']);
            self::assertSame('/var', $report['disk']['path']);
            self::assertNotNull($report['effective_uid']);
            if (function_exists('posix_geteuid')) {
                self::assertSame(posix_geteuid(), $report['effective_uid']);
            }
            $encoded = json_encode($report, JSON_THROW_ON_ERROR);
            self::assertStringNotContainsString('127.0.0.1', $encoded);
            self::assertStringNotContainsString('local_address', $encoded);
            self::assertStringNotContainsString('inode', $encoded);
            self::assertFalse($report['host_changes']);
        } finally {
            fclose($tcp);
            fclose($udp);
        }
    }

    public function testRealLinuxIpv6TcpAndUdpBindingsAreObservedWhenIpv6IsEnabled(): void
    {
        $this->requireLinux();
        $tcp = @stream_socket_server('tcp://[::1]:0', $tcpError, $tcpMessage);
        $udp = @stream_socket_server('udp://[::1]:0', $udpError, $udpMessage, STREAM_SERVER_BIND);
        if (!is_resource($tcp) || !is_resource($udp)) {
            if (is_resource($tcp)) {
                fclose($tcp);
            }
            if (is_resource($udp)) {
                fclose($udp);
            }
            self::markTestSkipped('IPv6 loopback is unavailable in this Linux network namespace.');
        }
        try {
            $report = LinuxHostInventory::collect();
            self::assertNotNull($report['network']['tcp6']);
            self::assertNotNull($report['network']['udp6']);
            self::assertContains($this->port($tcp), $report['network']['tcp6']);
            self::assertContains($this->port($udp), $report['network']['udp6']);
            self::assertFalse($report['host_changes']);
        } finally {
            fclose($tcp);
            fclose($udp);
        }
    }

    private function requireLinux(): void
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            self::markTestSkipped('Requires the actual Linux procfs/network namespace in CI.');
        }
    }

    /** @param resource $socket */
    private function port($socket): int
    {
        $address = stream_socket_get_name($socket, false);
        self::assertIsString($address);
        self::assertMatchesRegularExpression('/:[1-9][0-9]*$/', $address);

        return (int) substr($address, (int) strrpos($address, ':') + 1);
    }
}
