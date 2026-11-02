<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Platform\RuntimePlan;
use Wcp360\Core\Shared\Platform\TargetPlatform;

final class RuntimePlanTest extends TestCase
{
    public function testAllFivePlatformsSelectIsolatedDevelopmentRuntimeWithoutChangingNativePackages(): void
    {
        foreach (TargetPlatform::profiles() as $profile) {
            $plan = RuntimePlan::forPlatform($profile);
            self::assertSame($profile['id'], $plan['platform']);
            self::assertSame($profile['native_php_series'] === '8.4', $plan['native_core_php_matches']);
            self::assertSame('/opt/wcp360/runtime/php/8.4/bin/php', $plan['php']['cli_binary']);
            self::assertSame('/opt/wcp360/runtime/php/8.4/sbin/php-fpm', $plan['php']['fpm_binary']);
            self::assertSame('development-source-build', $plan['php']['status']);
            self::assertSame('not_installed', $plan['php']['service_profile']);
            self::assertFalse($plan['php']['native_packages_modified']);
            self::assertSame(17, $plan['postgresql']['major']);
            self::assertSame('isolated-verification-service', $plan['postgresql']['mode']);
            self::assertFalse($plan['postgresql']['local_service_installed']);
            self::assertSame('not_checked', $plan['runtime_validation']);
            self::assertFalse($plan['host_changes']);
            self::assertFalse($plan['qualified_release']);
        }
    }

    public function testRejectsAProfileWithUnreviewedIdentityOrFields(): void
    {
        $profile = TargetPlatform::profiles()[0];
        $profile['id'] = 'ubuntu-25.10';
        $this->expectException(\RuntimeException::class);
        RuntimePlan::forPlatform($profile);
    }

    public function testProviderObjectOrderDoesNotMatterButTypesAndPinnedInputsDo(): void
    {
        $source = dirname(__DIR__, 2).'/config/platforms/runtime-providers.json';
        $catalogue = json_decode((string) file_get_contents($source), true, 16, JSON_THROW_ON_ERROR);
        $temporary = tempnam(sys_get_temp_dir(), 'wcp-plan-');
        self::assertIsString($temporary);
        try {
            $reordered = array_reverse($catalogue, true);
            $reordered['php'] = array_reverse($reordered['php'], true);
            file_put_contents($temporary, json_encode($reordered, JSON_THROW_ON_ERROR));
            self::assertSame($catalogue['php']['version'], RuntimePlan::forPlatform(TargetPlatform::profiles()[0], $temporary)['php']['version']);
            $futurePatch = $catalogue;
            $futurePatch['php']['version'] = '8.4.999';
            $futurePatch['php']['source_url'] = 'https://www.php.net/distributions/php-8.4.999.tar.xz';
            $futurePatch['php']['sha256'] = str_repeat('1', 64);
            file_put_contents($temporary, json_encode($futurePatch, JSON_THROW_ON_ERROR));
            self::assertSame('8.4.999', RuntimePlan::forPlatform(TargetPlatform::profiles()[0], $temporary)['php']['version']);
            foreach ([
                ['php', 'source_url', 'https://unreviewed.invalid/php.tar.xz'],
                ['php', 'prefix', '/usr'],
                ['php', 'sha256', 'not-a-sha256'],
                ['php', 'version', '8.5.0'],
                ['postgresql', 'major', '17'],
                ['postgresql', 'image', 'postgres:18'],
                ['php', 'command', 'arbitrary'],
            ] as [$section, $field, $value]) {
                $changed = $catalogue;
                $changed[$section][$field] = $value;
                file_put_contents($temporary, json_encode($changed, JSON_THROW_ON_ERROR));
                try {
                    RuntimePlan::forPlatform(TargetPlatform::profiles()[0], $temporary);
                    self::fail('Unreviewed provider input was accepted.');
                } catch (\RuntimeException $error) {
                    self::assertStringContainsString('reviewed development profile', $error->getMessage());
                }
            }
        } finally {
            unlink($temporary);
        }
    }

    public function testMalformedProviderDataFailsClosed(): void
    {
        $temporary = tempnam(sys_get_temp_dir(), 'wcp-plan-');
        self::assertIsString($temporary);
        try {
            file_put_contents($temporary, '{');
            $this->expectException(\RuntimeException::class);
            RuntimePlan::forPlatform(TargetPlatform::profiles()[0], $temporary);
        } finally {
            unlink($temporary);
        }
    }
}
