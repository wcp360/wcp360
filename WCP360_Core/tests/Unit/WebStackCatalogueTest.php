<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\WebStackCatalogue;

final class WebStackCatalogueTest extends TestCase
{
    public function testFourProfilesDistinguishPlansNativeInstallationAndMigration(): void
    {
        $report = WebStackCatalogue::report();
        self::assertSame('wcp360.candidate.web-stacks/1', $report['schema']);
        self::assertSame('classic', $report['default']);
        self::assertSame(['classic','modern','legacy','advanced'], array_column($report['choices'], 'id'));
        foreach (['native_installation_available','migration_available','multi_php_available','host_changes','qualified_release'] as $flag) self::assertFalse($report[$flag]);
        self::assertSame('nginx_fpm', WebStackCatalogue::choice('classic')['driver']);
        self::assertSame('isolated_candidate_only', WebStackCatalogue::choice('classic')['activation']);
    }

    public function testWorkerOptInOnlyEnablesExistingAdvancedPlanningNotStandaloneModern(): void
    {
        self::assertFalse(WebStackCatalogue::choice('advanced')['selectable_for_plan']);
        self::assertTrue(WebStackCatalogue::choice('advanced', true)['selectable_for_plan']);
        self::assertSame('unimplemented', WebStackCatalogue::choice('advanced', true)['activation']);
        foreach (['modern','legacy'] as $id) {
            $choice = WebStackCatalogue::choice($id, true);
            self::assertFalse($choice['selectable_for_plan']);
            self::assertFalse($choice['plan_implemented']);
            self::assertNull($choice['selection_value']);
        }
    }

    public function testExistingEngineValuesRetainTheirTopologyAndUnknownValuesNeverFallBack(): void
    {
        self::assertSame('classic', WebStackCatalogue::forApplicationServer('php_fpm'));
        self::assertSame('advanced', WebStackCatalogue::forApplicationServer('frankenphp_worker'));
        foreach (['', 'CLASSIC','apache','private-stack-input/../../','nginx;private-stack-input'] as $value) {
            try { WebStackCatalogue::forApplicationServer($value); self::fail('Unknown engine accepted.'); }
            catch (\InvalidArgumentException $error) { self::assertStringNotContainsString('private-stack-input', $error->getMessage()); }
            try { WebStackCatalogue::choice($value); self::fail('Unknown profile accepted.'); }
            catch (\InvalidArgumentException $error) { self::assertStringNotContainsString('private-stack-input', $error->getMessage()); }
        }
    }

    public function testMutatingAReportCannotEnableAnotherProfileOrChangeTheDefault(): void
    {
        $report = WebStackCatalogue::report(true);
        $report['default'] = 'modern';
        $report['choices'][1]['selectable_for_plan'] = true;
        self::assertSame('classic', WebStackCatalogue::report()['default']);
        self::assertFalse(WebStackCatalogue::choice('modern', true)['selectable_for_plan']);
    }

    public function testStandaloneCatalogueNeedsNoFrameworkBootstrapOrRecognizedHost(): void
    {
        foreach ([false,true] as $worker) {
            $process = $this->cli($worker ? ['--web-stacks','--worker-plans'] : ['--web-stacks']);
            self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
            self::assertSame(WebStackCatalogue::report($worker), json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR));
            self::assertSame('', $process->getErrorOutput());
        }
    }

    public function testStandaloneNoArgumentInspectionRetainsItsOriginalProjection(): void
    {
        $process = $this->cli([]);
        $data = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['supported_target','plan','host_changes','qualified_release'], array_keys($data));
        self::assertSame($data['supported_target'] ? 0 : 78, $process->getExitCode());
        self::assertFalse($data['host_changes']);
        self::assertFalse($data['qualified_release']);
    }

    public function testUnsupportedOrNonOptedInSelectionsRemainVisibleButFailPlanningAdmission(): void
    {
        foreach ([['--web-stack=modern'], ['--web-stack=legacy','--worker-plans'], ['--web-stack=advanced']] as $args) {
            $process = $this->cli($args);
            self::assertSame(78, $process->getExitCode());
            $data = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
            self::assertFalse($data['hosting_web_stack']['selectable_for_plan']);
            self::assertFalse($data['native_installation_available']);
            self::assertFalse($data['host_changes']);
            self::assertNotSame('classic', $data['hosting_web_stack']['id']);
        }
        $process = $this->cli(['--web-stack=advanced','--worker-plans']);
        $data = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('advanced', $data['hosting_web_stack']['id']);
        self::assertTrue($data['hosting_web_stack']['selectable_for_plan']);
        self::assertSame($data['supported_target'] ? 0 : 78, $process->getExitCode());
        self::assertFalse($data['native_installation_available']);
    }

    public function testUnknownDuplicateOversizedAndConflictingCliFlagsRefuseWithoutInputReflection(): void
    {
        foreach ([['--private-stack-input'], ['--web-stacks','--web-stacks'], ['--worker-plans'],
            ['--web-stack=classic','--web-stacks'], ['--web-stack=classic','--web-stack=modern'],
            ['--web-stack=private-stack-input'], ['--web-stacks','--worker-plans','--worker-plans'],
            ['--web-stack='.str_repeat('x', 100)]] as $args) {
            $process = $this->cli($args);
            self::assertSame(64, $process->getExitCode());
            self::assertSame('', $process->getOutput());
            self::assertStringNotContainsString('private-stack-input', $process->getErrorOutput());
        }
    }

    private function cli(array $arguments): Process
    {
        $process = new Process([PHP_BINARY, dirname(__DIR__, 2).'/installer/preflight/runtime-plan.php', ...$arguments], null, null, null, 10);
        $process->run();
        return $process;
    }
}
