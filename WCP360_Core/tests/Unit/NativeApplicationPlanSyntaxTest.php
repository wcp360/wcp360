<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;
use Wcp360\Core\Module\Hosting\Infrastructure\CaddySyntaxProbe;
use Wcp360\Core\Module\Hosting\Infrastructure\NativeApplicationPlanSyntax;
use Wcp360\Core\Module\Hosting\Infrastructure\NativeSyntaxTools;
use Wcp360\Core\Module\Hosting\Infrastructure\NginxSyntaxProbe;
use Wcp360\Core\Module\Hosting\Infrastructure\SyntaxProcessRunner;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;

final class NativeApplicationPlanSyntaxTest extends TestCase
{
    public function testEveryRecipeAndProfileReportsMissingProvidersInArtifactOrderWithoutRunningOrFallingBack(): void
    {
        $expectations = [
            'nginx_php_fpm' => [['nginx'], ['nginx' => 'not_configured', 'php_fpm' => 'not_run']],
            'nginx_frankenphp' => [['nginx', 'frankenphp'],
                ['nginx' => 'not_configured', 'caddy' => 'not_configured', 'systemd_unit' => 'not_run']],
            'apache_php_fpm' => [[], ['apache' => 'not_configured', 'php_fpm' => 'not_run', 'htaccess' => 'not_run']],
            'apache_frankenphp' => [['frankenphp'],
                ['apache' => 'not_configured', 'caddy' => 'not_configured', 'systemd_unit' => 'not_run']],
            'caddy_php_fpm' => [['caddy'], ['caddy' => 'not_configured', 'php_fpm' => 'not_run']],
            'caddy_frankenphp' => [['frankenphp'], ['caddy' => 'not_configured', 'systemd_unit' => 'not_run']],
        ];
        $combinations = 0;

        foreach (ApplicationRecipeCatalogue::report()['recipes'] as $choice) {
            foreach (ApplicationRecipeCatalogue::profiles() as $profile) {
                [$expectedTools, $expectedStatuses] = $expectations[$choice['id']];
                $fields = ['schema' => 'wcp360.candidate.application-plan/1', 'application' => $profile['id'],
                    'recipe' => $choice['id'], 'web_server' => $choice['web_server'],
                    'application_server' => $choice['application_server']];
                foreach ($choice['artifacts'] as $artifact) $fields[$artifact] = "synthetic:$artifact\n";
                $document = ApplicationPlanDocument::fromArray($fields, $choice['id'], $profile['id']);

                $runner = $this->createMock(SyntaxProcessRunner::class);
                $runner->expects(self::never())->method('run');
                $tools = $this->createMock(NativeSyntaxTools::class);
                $calls = [];
                $tools->expects(self::exactly(count($expectedTools)))->method('binary')->willReturnCallback(
                    static function (string $tool) use (&$calls): ?string {
                        $calls[] = $tool;
                        return null;
                    });
                $syntax = new NativeApplicationPlanSyntax(new NginxSyntaxProbe($runner, $tools), new CaddySyntaxProbe($runner, $tools));

                self::assertSame($expectedStatuses, $syntax->check($document), $choice['id'].'/'.$profile['id']);
                self::assertSame($expectedTools, $calls, 'Only the matching fixed provider may be queried.');
                self::assertSame(array_keys($document->files()), array_keys($expectedStatuses));
                self::assertSame($fields, $document->fields());
                ++$combinations;
            }
        }

        self::assertSame(18, $combinations);
    }

    public function testUnknownToolNamesPathsAndCommandFragmentsAreRefusedBeforePlatformChecks(): void
    {
        $tools = new NativeSyntaxTools();
        foreach (['apache', 'php_fpm', '/usr/sbin/nginx', '../nginx', 'nginx; private-command'] as $tool) {
            try {
                $tools->binary($tool);
                self::fail('An unrecognized syntax provider was admitted.');
            } catch (\InvalidArgumentException $exception) {
                self::assertSame('Unknown syntax tool.', $exception->getMessage());
            }
        }
    }
}