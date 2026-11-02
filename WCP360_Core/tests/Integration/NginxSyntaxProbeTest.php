<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\Hosting\Infrastructure\NativeSyntaxTools;
use Wcp360\Core\Module\Hosting\Infrastructure\NginxSyntaxProbe;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

final class NginxSyntaxProbeTest extends KernelTestCase
{
    /** @return iterable<string,array{string,string}> */
    public static function recipes(): iterable
    {
        foreach (['nginx_php_fpm', 'nginx_frankenphp', 'nginx_apache_php_fpm'] as $recipe) {
            foreach (['php', 'front_controller', 'wordpress'] as $application) {
                yield $recipe.'/'.$application => [$recipe, $application];
            }
        }
    }

    /** @return list<string> */
    private function temporaryDirectories(): array
    {
        $directories = glob('/tmp/wcp360-native-syntax-*', GLOB_ONLYDIR);
        if ($directories === false) throw new \RuntimeException('Private syntax directory inspection unavailable.');
        sort($directories);
        return $directories;
    }

    private function requireNginx(): void
    {
        if (self::getContainer()->get(NativeSyntaxTools::class)->binary('nginx') === null) {
            self::markTestSkipped('Native Nginx verification is not configured for this runtime/identity.');
        }
    }

    #[DataProvider('recipes')]
    public function testGeneratedApplicationArtifactPassesNativeSyntaxWithoutCreatingRuntimePaths(string $recipe, string $application): void
    {
        self::bootKernel();
        $this->requireNginx();
        $before = $this->temporaryDirectories();
        $site = WorkbenchStore::id();
        $root = '/srv/wcp360-candidate/applications/'.$site;
        $socket = '/run/wcp360-applications/'.$site;
        self::assertFalse(file_exists($root));
        self::assertFalse(file_exists($socket));
        $plan = self::getContainer()->get(GoPlanRenderer::class)->renderApplication($site, 'native-syntax.test', $recipe, $application);
        $content = $plan['nginx'];
        self::assertSame('passed', self::getContainer()->get(NginxSyntaxProbe::class)->check($content));
        self::assertFalse(file_exists($root));
        self::assertFalse(file_exists($socket));
        self::assertSame($before, $this->temporaryDirectories());
    }

    public function testInvalidNativeSyntaxFailsAndRemovesItsPrivateTemporaryFiles(): void
    {
        self::bootKernel();
        $this->requireNginx();
        $before = $this->temporaryDirectories();
        $content = "server {\nlisten 127.0.0.1:8082;\nprivate_invalid_directive on;\n}\n";
        self::assertSame('failed', self::getContainer()->get(NginxSyntaxProbe::class)->check($content));
        self::assertSame($before, $this->temporaryDirectories());
    }
}
