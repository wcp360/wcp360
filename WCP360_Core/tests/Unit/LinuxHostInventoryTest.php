<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Platform\LinuxHostInventory;

final class LinuxHostInventoryTest extends TestCase
{
    private const HEADER = "  sl  local_address rem_address   st tx_queue rx_queue tr tm->when retrnsmt   uid  timeout inode\n";

    public function testMemoryUsesAvailableEstimateRatherThanOnlyFreePages(): void
    {
        $memory = "MemTotal:        4194304 kB\nMemFree:          100000 kB\nMemAvailable:    3145728 kB\nHugePages_Total:       0\nDirectMap1G:     2097152 kB\n";
        self::assertSame(['total_bytes' => 4294967296, 'available_bytes' => 3221225472], LinuxHostInventory::parseMemory($memory));
        self::assertSame(['total_bytes' => 1024, 'available_bytes' => 0], LinuxHostInventory::parseMemory("MemTotal: 1 kB\nMemAvailable: 0 kB\n"));
    }

    public function testUnknownOrContradictoryMemoryCannotAppearAsUsableCapacity(): void
    {
        foreach ([
            '', "MemTotal: 10 kB\n", "MemTotal: 10 kB\nMemAvailable: 1 kB",
            "MemTotal: 0 kB\nMemAvailable: 0 kB\n", "MemTotal: 10 kB\nMemAvailable: 11 kB\n",
            "MemTotal: 10 kB\nMemAvailable: -1 kB\n", "MemTotal: 10 MB\nMemAvailable: 1 kB\n",
            "MemTotal: 10 kB\nMemAvailable: 1\n", "MemTotal: 10 kB\nMemAvailable: 1 kB\nMemAvailable: 2 kB\n",
            "MemTotal: 10 kB\nMemAvailable: 1 kB\ninvalid row\n", "MemTotal: 10 kB\r\nMemAvailable: 1 kB\r\n",
            "MemTotal: ".PHP_INT_MAX." kB\nMemAvailable: 1 kB\n", "MemTotal: 10 kB\nMemAvailable: 1.5 kB\n",
            "MemTotal: 10 kB\nMemAvailable: 1 kB\n".str_repeat(' ', 2097152)."\n",
        ] as $memory) {
            self::assertNull(LinuxHostInventory::parseMemory($memory), substr($memory, 0, 80));
        }
    }

    public function testTcpCountsOnlyListenersAndConservativelyIncludesLoopbackAndWildcardBindings(): void
    {
        $table = self::HEADER
            .$this->row('0100007F:1F90', '01', '0100007F:0050')
            .$this->row('0100007F:01BB', '0A')
            .$this->row('00000000:0050', '0A')
            .$this->row('0100007F:0050', '0A')
            .$this->row('00000000:FFFF', '0a')
            .$this->row('00000000:0000', '0A');
        self::assertSame([80, 443, 65535], LinuxHostInventory::parseNetwork($table, 'tcp', '4'));
        self::assertSame([], LinuxHostInventory::parseNetwork(self::HEADER, 'tcp', '4'));
        self::assertSame([], LinuxHostInventory::parseNetwork(self::HEADER.$this->row('0100007F:01BB', '06', '0100007F:0050', ''), 'tcp', '4'));
    }

    public function testUdpCountsConnectedAndUnconnectedBoundPortsRegardlessOfState(): void
    {
        $header = rtrim(self::HEADER, "\n")." ref pointer drops\n";
        $table = $header.$this->row('0100007F:0035', '07').$this->row('0100007F:01BB', '01', '0100007F:0200').$this->row('00000000:0000', '07');
        self::assertSame([53, 443], LinuxHostInventory::parseNetwork($table, 'udp', '4'));
    }

    public function testIpv6WildcardLoopbackAndMappedAddressesAlsoOccupyTheirPort(): void
    {
        $header = str_replace('rem_address', 'remote_address', self::HEADER);
        $remote = str_repeat('0', 32).':0000';
        $table = $header
            .$this->row(str_repeat('0', 32).':0050', '0A', $remote)
            .$this->row('00000000000000000000000001000000:01BB', '0A', $remote)
            .$this->row('0000000000000000FFFF00000100007F:20FB', '0A', $remote);
        self::assertSame([80, 443, 8443], LinuxHostInventory::parseNetwork($table, 'tcp', '6'));
        self::assertNull(LinuxHostInventory::parseNetwork($table, 'tcp', '4'));
        self::assertSame([80, 443, 8443], LinuxHostInventory::parseNetwork($table, 'udp', '6'));
    }

    public function testMalformedMissingOrTruncatedNetworkDataNeverLooksLikeNoListeners(): void
    {
        $row = $this->row('00000000:0050', '0A');
        foreach ([
            '', "\n", "local_address rem_address st\n", str_replace('inode', 'other', self::HEADER),
            self::HEADER."\n", self::HEADER.rtrim($row, "\n"), self::HEADER.'0: 00000000:0050 00000000:0000 0A'."\n",
            self::HEADER.str_replace('00000000:0050', '00000000:10000', $row),
            self::HEADER.str_replace('00000000:0050', '0000000G:0050', $row),
            self::HEADER.str_replace(' 0A ', ' ZZ ', $row),
            self::HEADER.str_replace('00000000:00000000', '00000000:invalid!', $row),
            self::HEADER.str_replace('1000 0 42', '-1 0 42', $row),
            self::HEADER.str_replace('1000 0 42', '1000 0 999999999999999999999', $row),
            self::HEADER.str_replace('1000 0 42', "1000\0 0 42", $row),
            self::HEADER.$row.self::HEADER,
            self::HEADER.str_repeat(' ', 2097152)."\n",
            self::HEADER.$this->row('00000000:0050', '0A', '00000000:0000', str_repeat('1', 2050)),
        ] as $table) {
            self::assertNull(LinuxHostInventory::parseNetwork($table, 'tcp', '4'), substr($table, 0, 160));
        }
        self::assertNull(LinuxHostInventory::parseNetwork(self::HEADER, 'raw', '4'));
        self::assertNull(LinuxHostInventory::parseNetwork(self::HEADER, 'tcp', 'IPv4'));
    }

    public function testCollectorHasNoMutableOrServiceHealthClaimAndOnlyProductPaths(): void
    {
        $report = LinuxHostInventory::collect();
        self::assertSame('current_process_namespace', $report['scope']);
        self::assertFalse($report['host_changes']);
        self::assertSame(LinuxHostInventory::MANAGED_PATHS, array_keys($report['managed_paths']));
        self::assertSame(['tcp4', 'tcp6', 'udp4', 'udp6'], array_keys($report['network']));
        self::assertSame(php_uname('m'), $report['architecture']);
        foreach ($report['managed_paths'] as $state) {
            self::assertContains($state, ['absent', 'present', 'unknown']);
        }
        self::assertArrayNotHasKey('native_apply_allowed', $report);
        self::assertArrayNotHasKey('service_health', $report);
        self::assertArrayNotHasKey('hostname', $report);
        if (PHP_OS_FAMILY !== 'Linux') {
            self::assertNull($report['platform']);
            self::assertNull($report['effective_uid']);
            self::assertNull($report['memory']);
            self::assertNull($report['disk']);
            self::assertNull($report['systemd_marker']);
            self::assertSame(['tcp4' => null, 'tcp6' => null, 'udp4' => null, 'udp6' => null], $report['network']);
            self::assertSame(array_fill_keys(LinuxHostInventory::MANAGED_PATHS, 'unknown'), $report['managed_paths']);
        }
    }

    public function testCapacityOnlyCollectionLeavesEveryNetworkObservationUnchecked(): void
    {
        $report = LinuxHostInventory::collect(false);
        self::assertSame(['tcp4' => null, 'tcp6' => null, 'udp4' => null, 'udp6' => null], $report['network']);
        self::assertFalse($report['host_changes']);
        self::assertSame('current_process_namespace', $report['scope']);
        self::assertSame(php_uname('m'), $report['architecture']);
    }

    private function row(string $local, string $state, string $remote = '00000000:0000', string $tail = '1 0000000000000000 100 0 0 10 0'): string
    {
        return "  0: $local $remote $state 00000000:00000000 00:00000000 00000000 1000 0 42".($tail === '' ? '' : ' '.$tail)."\n";
    }
}
