<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;

/** Accident-prevention gate; root/hypervisor administrators remain trusted. */
final class DisposableExecution
{
    public static function requireEnvironment(): void
    {
        $container = getenv('WCP_ISOLATED_TEST_CONTAINER');
        $vm = getenv('WCP_ISOLATED_TEST_VM');
        if (PHP_OS_FAMILY === 'Linux' && $container === '1' && ($vm === false || $vm === '') && is_file('/.dockerenv') && !is_link('/.dockerenv')) return;
        if (PHP_OS_FAMILY === 'Linux' && $vm === '1' && ($container === false || $container === '') && !file_exists('/.dockerenv') && !is_link('/.dockerenv')) {
            $path = '/run/wcp360-candidate-vm';
            $stat = @lstat($path);
            if ($stat !== false && ($stat['mode'] & 0170000) === 0100000 && $stat['size'] <= 128) {
                $marker = @file_get_contents($path);
                $boot = @file_get_contents('/proc/sys/kernel/random/boot_id');
                $product = @file_get_contents('/sys/class/dmi/id/product_name');
                $release = @file_get_contents('/etc/os-release');
                if (is_string($marker) && is_string($boot) && is_string($product) && is_string($release) && self::acceptsVmEvidence($marker, $boot, $product, $release, $stat['uid'], $stat['mode'])) return;
            }
        }
        throw new \RuntimeException('Provisioning requires an explicitly prepared disposable test container or Debian 13 VM.');
    }

    /** @internal Deterministic validation, shared by the gate and its regression tests. */
    public static function acceptsVmEvidence(string $marker, string $boot, string $product, string $release, int $uid, int $mode): bool
    {
        return $uid === 0 && ($mode & 0022) === 0
            && preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\n\z/', $boot) === 1
            && $marker === "wcp360.candidate.vm/1\n".$boot
            && trim($product) === 'WCP360 disposable candidate VM'
            && str_contains("\n".$release, "\nID=debian\n")
            && str_contains("\n".$release, "\nVERSION_ID=\"13\"\n");
    }
}
