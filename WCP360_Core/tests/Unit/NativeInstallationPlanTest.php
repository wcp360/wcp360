<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Application\NativeInstallationPlan;
use Wcp360\Core\Shared\Platform\LinuxHostInventory;
use Wcp360\Core\Shared\Platform\TargetPlatform;

final class NativeInstallationPlanTest extends TestCase
{
    public function testOmittedRequirementsNeverInventProductionDefaults(): void
    {
        $requirements = NativeInstallationPlan::parseArguments([]);
        self::assertSame([
            'tcp_ports' => null, 'udp_ports' => null,
            'memory_available_min_bytes' => null, 'disk_free_min_bytes' => null,
        ], $requirements);
        $report = NativeInstallationPlan::fromInventory($this->inventory(), $requirements);
        foreach (['requested_tcp_ports', 'requested_udp_ports', 'memory_available', 'disk_free_on_var'] as $check) {
            self::assertSame('not_checked', $report['checks'][$check]);
            self::assertContains($check, $report['blockers']);
        }
        self::assertSame('operator_requested_checks_not_production_defaults', $report['requirements_source']);
    }

    public function testExplicitRequirementsNormalizePortsAndUseMebibytes(): void
    {
        self::assertSame([
            'tcp_ports' => [80, 443, 65535], 'udp_ports' => [53],
            'memory_available_min_bytes' => 2147483648, 'disk_free_min_bytes' => 10737418240,
        ], NativeInstallationPlan::parseArguments(['--disk-mib=10240', '--tcp=65535,443,80', '--memory-mib=2048', '--udp=53']));
        $report = NativeInstallationPlan::fromInventory($this->inventory(), NativeInstallationPlan::parseArguments(['--tcp=none', '--udp=none']));
        self::assertSame([], $report['requirements']['tcp_ports']);
        self::assertSame([], $report['requirements']['udp_ports']);
        self::assertSame('pass', $report['checks']['requested_tcp_ports']);
        self::assertSame('pass', $report['checks']['requested_udp_ports']);
        self::assertFalse($report['native_apply_allowed']);
    }

    public function testInvalidArgumentsAreRejectedWithoutEchoingValues(): void
    {
        foreach ([
            ['--apply'], ['--tcp='], ['--tcp=0'], ['--tcp=65536'], ['--tcp=080'],
            ['--tcp=80,80'], ['--tcp=80,'], ['--tcp= 80'], ['--tcp=80;secret-marker'],
            ['--tcp=80', '--tcp=443'], ['--udp=-1'], ['--udp=NONE'],
            ['--memory-mib=0'], ['--memory-mib=01'], ['--memory-mib=1.5'],
            ['--disk-mib=16777217'], ["--tcp=80\nsecret-marker"], ["--tcp=80\0secret-marker"],
            ['--disk-mib='.str_repeat('1', 512)], ['--tcp='.implode(',', range(1, 33))],
            ['--tcp=80', '--udp=53', '--memory-mib=1', '--disk-mib=1', '--extra=secret-marker'],
        ] as $arguments) {
            try {
                NativeInstallationPlan::parseArguments($arguments);
                self::fail('An invalid inspection argument was accepted.');
            } catch (\InvalidArgumentException $error) {
                self::assertSame('Invalid native inspection arguments.', $error->getMessage());
            }
        }
    }

    public function testPassingHostFactsOnAllTargetsCannotAuthorizeNativeInstallation(): void
    {
        foreach (TargetPlatform::profiles() as $profile) {
            $inventory = $this->inventory();
            $inventory['platform'] = $profile['id'];
            $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
            foreach (['declared_target', 'amd64', 'operator_identity_observed', 'memory_available', 'disk_free_on_var', 'requested_tcp_ports', 'requested_udp_ports', 'network_observation', 'fresh_managed_paths', 'systemd_marker'] as $check) {
                self::assertSame('pass', $report['checks'][$check], $profile['id'].' '.$check);
            }
            foreach (['native_runtime_provider', 'artifact_identity_and_trust', 'registered_installation_effects'] as $gate) {
                self::assertSame('blocked', $report['checks'][$gate]);
                self::assertContains($gate, $report['blockers']);
            }
            foreach (['cgroup_capacity', 'product_filesystem_capacity', 'host_network_namespace', 'dns_and_time', 'service_ownership_and_health'] as $check) {
                self::assertSame('not_checked', $report['checks'][$check]);
                self::assertContains($check, $report['blockers']);
            }
            self::assertFalse($report['canonical_execution_plan']);
            self::assertFalse($report['native_apply_allowed']);
            self::assertFalse($report['host_changes']);
            self::assertFalse($report['qualified_release']);
            self::assertSame($profile['id'], $report['development_runtime_reference']['platform']);
            self::assertSame('development-source-build', $report['development_runtime_reference']['php']['status']);
            self::assertSame('/opt/wcp360/runtime/php/8.4/bin/php', $report['development_runtime_reference']['php']['cli_binary']);
            self::assertSame(LinuxHostInventory::MANAGED_PATHS, array_keys($report['native_layout_reference']['paths']));
            self::assertFalse($report['native_layout_reference']['applied']);
        }
    }

    public function testConflictsAcrossAddressFamiliesStayProtocolSpecific(): void
    {
        $inventory = $this->inventory();
        $inventory['network'] = ['tcp4' => [80], 'tcp6' => [443, 80], 'udp4' => [443], 'udp6' => [53]];
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame(['tcp' => [80, 443], 'udp' => [53]], $report['port_conflicts']);
        self::assertSame('fail', $report['checks']['requested_tcp_ports']);
        self::assertSame('fail', $report['checks']['requested_udp_ports']);
        $inventory['network'] = ['tcp4' => [53], 'tcp6' => [], 'udp4' => [80, 443], 'udp6' => []];
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame(['tcp' => [], 'udp' => []], $report['port_conflicts']);
        self::assertSame('pass', $report['checks']['requested_tcp_ports']);
        self::assertSame('pass', $report['checks']['requested_udp_ports']);
    }

    public function testUnknownNetworkIsNeverFreeAndKnownConflictsStillFail(): void
    {
        $inventory = $this->inventory();
        $inventory['network'] = ['tcp4' => [], 'tcp6' => null, 'udp4' => null, 'udp6' => [53]];
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('unknown', $report['checks']['requested_tcp_ports']);
        self::assertSame('fail', $report['checks']['requested_udp_ports']);
        self::assertSame('unknown', $report['checks']['network_observation']);
        self::assertSame(['tcp6', 'udp4'], $report['unknown_network_tables']);
        $report = NativeInstallationPlan::fromInventory($inventory, NativeInstallationPlan::parseArguments(['--tcp=none', '--udp=none']));
        self::assertSame('pass', $report['checks']['requested_tcp_ports']);
        self::assertSame('pass', $report['checks']['requested_udp_ports']);
        self::assertSame('unknown', $report['checks']['network_observation']);
        self::assertContains('network_observation', $report['blockers']);
    }

    public function testCapacityUsesAvailableAndFreeValuesAndRetainsUnknowns(): void
    {
        $inventory = $this->inventory();
        $inventory['memory']['available_bytes'] = 1024;
        $inventory['disk']['free_bytes'] = 1024;
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('fail', $report['checks']['memory_available']);
        self::assertSame('fail', $report['checks']['disk_free_on_var']);
        $inventory['memory'] = null;
        $inventory['disk'] = null;
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('unknown', $report['checks']['memory_available']);
        self::assertSame('unknown', $report['checks']['disk_free_on_var']);
    }

    public function testExistingOrUnobservedProductPathsCannotBeApprovedForFreshInstall(): void
    {
        $inventory = $this->inventory();
        $inventory['managed_paths']['/usr/lib/wcp360'] = 'present';
        $inventory['managed_paths']['/var/lib/wcp360'] = 'unknown';
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('fail', $report['checks']['fresh_managed_paths']);
        self::assertContains('fresh_managed_paths', $report['blockers']);
        $inventory['managed_paths']['/usr/lib/wcp360'] = 'absent';
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('unknown', $report['checks']['fresh_managed_paths']);
        unset($inventory['managed_paths']['/var/lib/wcp360']);
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('unknown', $report['native_layout_reference']['paths']['/var/lib/wcp360']);
        self::assertFalse($report['native_layout_reference']['applied']);
    }

    public function testUnsupportedHostAndMissingIdentityFailWithoutRuntimeSubstitution(): void
    {
        $inventory = $this->inventory();
        $inventory['platform'] = 'debian-14';
        $inventory['architecture'] = 'aarch64';
        $inventory['effective_uid'] = null;
        $inventory['systemd_marker'] = false;
        $report = NativeInstallationPlan::fromInventory($inventory, $this->requirements());
        self::assertSame('fail', $report['checks']['declared_target']);
        self::assertSame('fail', $report['checks']['amd64']);
        self::assertSame('unknown', $report['checks']['operator_identity_observed']);
        self::assertSame('unknown', $report['checks']['systemd_marker']);
        self::assertNull($report['development_runtime_reference']);
        self::assertFalse($report['native_apply_allowed']);
    }

    private function requirements(): array
    {
        return NativeInstallationPlan::parseArguments(['--tcp=80,443', '--udp=53', '--memory-mib=2048', '--disk-mib=10240']);
    }

    private function inventory(): array
    {
        return [
            'scope' => 'current_process_namespace', 'platform' => 'debian-13',
            'architecture' => 'x86_64', 'effective_uid' => 1000,
            'memory' => ['total_bytes' => 8589934592, 'available_bytes' => 4294967296],
            'disk' => ['path' => '/var', 'total_bytes' => 107374182400, 'free_bytes' => 53687091200],
            'network' => ['tcp4' => [], 'tcp6' => [], 'udp4' => [], 'udp6' => []],
            'managed_paths' => array_fill_keys(LinuxHostInventory::MANAGED_PATHS, 'absent'),
            'systemd_marker' => true, 'host_changes' => false,
        ];
    }
}
