<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Application;

use Wcp360\Core\Shared\Platform\LinuxHostInventory;
use Wcp360\Core\Shared\Platform\RuntimePlan;
use Wcp360\Core\Shared\Platform\TargetPlatform;

/** Candidate inspection/handoff only; never an accepted IPC execution plan. */
final class NativeInstallationPlan
{
    private const REQUIREMENT_KEYS = ['tcp_ports', 'udp_ports', 'memory_available_min_bytes', 'disk_free_min_bytes'];

    /**
     * Operator-selected checks, not production defaults. No configuration is loaded.
     * @param list<string> $arguments
     * @return array{tcp_ports: list<int>|null, udp_ports: list<int>|null, memory_available_min_bytes: int|null, disk_free_min_bytes: int|null}
     */
    public static function parseArguments(array $arguments): array
    {
        $result = ['tcp_ports' => null, 'udp_ports' => null, 'memory_available_min_bytes' => null, 'disk_free_min_bytes' => null];
        $seen = [];
        if (count($arguments) > 4) {
            throw new \InvalidArgumentException('Invalid native inspection arguments.');
        }
        foreach ($arguments as $argument) {
            if (strlen($argument) > 512 || preg_match('/\A--(tcp|udp|memory-mib|disk-mib)=([^\r\n\x00]+)\z/D', $argument, $match) !== 1 || isset($seen[$match[1]])) {
                throw new \InvalidArgumentException('Invalid native inspection arguments.');
            }
            $seen[$match[1]] = true;
            $value = $match[2];
            if ($match[1] === 'tcp' || $match[1] === 'udp') {
                $ports = [];
                if ($value !== 'none') {
                    foreach (explode(',', $value) as $port) {
                        if (preg_match('/\A[1-9][0-9]{0,4}\z/D', $port) !== 1 || (int) $port > 65535 || in_array((int) $port, $ports, true)) {
                            throw new \InvalidArgumentException('Invalid native inspection arguments.');
                        }
                        $ports[] = (int) $port;
                    }
                }
                if (count($ports) > 32) {
                    throw new \InvalidArgumentException('Invalid native inspection arguments.');
                }
                sort($ports);
                $result[$match[1].'_ports'] = $ports;
            } else {
                if (preg_match('/\A[1-9][0-9]{0,7}\z/D', $value) !== 1 || (int) $value > min(16777216, intdiv(PHP_INT_MAX, 1048576))) {
                    throw new \InvalidArgumentException('Invalid native inspection arguments.');
                }
                $result[$match[1] === 'memory-mib' ? 'memory_available_min_bytes' : 'disk_free_min_bytes'] = (int) $value * 1048576;
            }
        }

        return $result;
    }

    /**
     * @param list<string> $arguments
     * @return array<string, mixed>
     */
    public static function forHost(array $arguments = []): array
    {
        // Validate arguments before reading machine state.
        $requirements = self::parseArguments($arguments);
        return self::fromInventory(LinuxHostInventory::collect(), $requirements);
    }

    /**
     * Internal seam for deterministic observation tests. Browser-supplied facts are
     * not an authority source; an eventual wizard must observe its server directly.
     * @param array{scope: string, platform: string|null, architecture: string, effective_uid: int|null, memory: array{total_bytes: int, available_bytes: int}|null, disk: array{path: string, total_bytes: int, free_bytes: int}|null, network: array{tcp4: list<int>|null, tcp6: list<int>|null, udp4: list<int>|null, udp6: list<int>|null}, managed_paths: array<string, string>, systemd_marker: bool|null, host_changes: false} $inventory
     * @param array{tcp_ports: list<int>|null, udp_ports: list<int>|null, memory_available_min_bytes: int|null, disk_free_min_bytes: int|null} $requirements
     * @return array<string, mixed>
     */
    public static function fromInventory(array $inventory, array $requirements): array
    {
        self::validateRequirements($requirements);
        $profile = null;
        foreach (TargetPlatform::profiles() as $candidate) {
            if ($candidate['id'] === $inventory['platform']) {
                $profile = $candidate;
                break;
            }
        }
        $checks = [
            'declared_target' => $profile === null ? 'fail' : 'pass',
            'amd64' => $inventory['architecture'] === 'x86_64' ? 'pass' : 'fail',
            'operator_identity_observed' => $inventory['effective_uid'] === null ? 'unknown' : 'pass',
            'memory_available' => self::capacity($requirements['memory_available_min_bytes'], $inventory['memory']['available_bytes'] ?? null),
            'disk_free_on_var' => self::capacity($requirements['disk_free_min_bytes'], $inventory['disk']['free_bytes'] ?? null),
        ];
        $conflicts = [];
        $unknownTables = [];
        foreach ($inventory['network'] as $table => $ports) {
            if ($ports === null) {
                $unknownTables[] = $table;
            }
        }
        foreach (['tcp', 'udp'] as $protocol) {
            $required = $requirements[$protocol.'_ports'];
            $observed = array_merge($inventory['network'][$protocol.'4'] ?? [], $inventory['network'][$protocol.'6'] ?? []);
            $conflicts[$protocol] = $required === null ? [] : array_values(array_unique(array_intersect($required, $observed)));
            sort($conflicts[$protocol]);
            $checks['requested_'.$protocol.'_ports'] = $required === null ? 'not_checked' : ($conflicts[$protocol] !== [] ? 'fail' : ($required === [] ? 'pass' : (($inventory['network'][$protocol.'4'] === null || $inventory['network'][$protocol.'6'] === null) ? 'unknown' : 'pass')));
        }
        $checks['network_observation'] = $unknownTables === [] ? 'pass' : 'unknown';
        $managedPaths = [];
        foreach (LinuxHostInventory::MANAGED_PATHS as $path) {
            $managedPaths[$path] = $inventory['managed_paths'][$path] ?? 'unknown';
        }
        $checks['fresh_managed_paths'] = in_array('present', $managedPaths, true) ? 'fail' : (in_array('unknown', $managedPaths, true) ? 'unknown' : 'pass');
        // A marker is not service health, root permission or host-wide qualification.
        $checks['systemd_marker'] = $inventory['systemd_marker'] === true ? 'pass' : 'unknown';
        $checks += [
            'cgroup_capacity' => 'not_checked',
            'product_filesystem_capacity' => 'not_checked',
            'host_network_namespace' => 'not_checked',
            'dns_and_time' => 'not_checked',
            'service_ownership_and_health' => 'not_checked',
            'native_runtime_provider' => 'blocked',
            'artifact_identity_and_trust' => 'blocked',
            'registered_installation_effects' => 'blocked',
        ];
        $blockers = array_keys(array_filter($checks, static fn (string $state): bool => $state !== 'pass'));

        return [
            'schema' => 'wcp360.candidate-native-install-plan/1',
            'mode' => 'fresh-host-inspection',
            'observed_at' => gmdate('Y-m-d\TH:i:s\Z'),
            'requirements_source' => 'operator_requested_checks_not_production_defaults',
            'requirements' => $requirements,
            'observations' => $inventory,
            'port_conflicts' => $conflicts,
            'unknown_network_tables' => $unknownTables,
            'checks' => $checks,
            'blockers' => $blockers,
            'native_layout_reference' => [
                'contract' => 'WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT',
                'paths' => $managedPaths,
                'process_identities' => ['wcp360-user', 'wcp360-admin', 'wcp360-worker', 'wcpd', 'root (minimal privileged broker)'],
                'applied' => false,
            ],
            'development_runtime_reference' => $profile === null ? null : RuntimePlan::forPlatform($profile),
            'observation_limits' => [
                'memory' => 'host-visible estimate; cgroup limits not checked',
                'disk' => 'current /var filesystem only; other mounts not checked',
                'network' => 'current network namespace; observations do not reserve ports or identify service owners',
                'managed_paths' => 'presence requires explicit review; no ownership or safe-overwrite claim',
                'systemd' => 'marker only; no service or boot-health verification',
            ],
            'canonical_execution_plan' => false,
            'native_apply_allowed' => false,
            'host_changes' => false,
            'qualified_release' => false,
        ];
    }

    private static function capacity(?int $required, ?int $observed): string
    {
        return $required === null ? 'not_checked' : ($observed === null ? 'unknown' : ($observed >= $required ? 'pass' : 'fail'));
    }

    /** @param array<string, mixed> $requirements */
    private static function validateRequirements(array $requirements): void
    {
        $keys = array_keys($requirements);
        sort($keys);
        $expected = self::REQUIREMENT_KEYS;
        sort($expected);
        if ($keys !== $expected) {
            throw new \InvalidArgumentException('Invalid native inspection requirements.');
        }
        foreach (['tcp_ports', 'udp_ports'] as $key) {
            $ports = $requirements[$key];
            if ($ports === null) {
                continue;
            }
            if (!is_array($ports) || !array_is_list($ports) || count($ports) > 32) {
                throw new \InvalidArgumentException('Invalid native inspection requirements.');
            }
            foreach ($ports as $port) {
                if (!is_int($port) || $port < 1 || $port > 65535) {
                    throw new \InvalidArgumentException('Invalid native inspection requirements.');
                }
            }
            if (count(array_unique($ports)) !== count($ports)) {
                throw new \InvalidArgumentException('Invalid native inspection requirements.');
            }
        }
        foreach (['memory_available_min_bytes', 'disk_free_min_bytes'] as $key) {
            if ($requirements[$key] !== null && (!is_int($requirements[$key]) || $requirements[$key] < 1)) {
                throw new \InvalidArgumentException('Invalid native inspection requirements.');
            }
        }
    }
}
