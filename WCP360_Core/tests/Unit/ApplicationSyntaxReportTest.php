<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;
use Wcp360\Core\Module\Hosting\Domain\ApplicationSyntaxReport;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;

final class ApplicationSyntaxReportTest extends TestCase
{
    public static function combinations(): iterable
    {
        foreach (ApplicationRecipeCatalogue::report()['recipes'] as $recipe) {
            foreach (ApplicationRecipeCatalogue::profiles() as $profile) {
                yield $recipe['id'].'/'.$profile['id'] => [$recipe['id'], $profile['id']];
            }
        }
    }

    private static function fields(string $recipe, string $application): array
    {
        $choice = ApplicationRecipeCatalogue::choice($recipe);
        $fields = ['schema' => 'wcp360.candidate.application-plan/1', 'application' => $application,
            'recipe' => $recipe, 'web_server' => $choice['web_server'], 'application_server' => $choice['application_server']];
        foreach ($choice['artifacts'] as $artifact) $fields[$artifact] = "synthetic:$recipe:$application:$artifact\n";
        return $fields;
    }

    private static function document(string $recipe = 'nginx_frankenphp', string $application = 'php'): ApplicationPlanDocument
    {
        return ApplicationPlanDocument::fromArray(self::fields($recipe, $application), $recipe, $application);
    }

    private static function statuses(): array
    {
        return ['nginx' => 'passed', 'caddy' => 'passed', 'systemd_unit' => 'passed'];
    }

    #[DataProvider('combinations')]
    public function testEveryRecipeAndProfileReportsEveryExactArtifactWithoutGrantingActivation(string $recipe, string $application): void
    {
        $document = self::document($recipe, $application);
        $before = $document->fields();
        $files = $document->files();
        $stages = [
            'nginx' => ['nginx', 'configuration_test'],
            'apache' => ['apache', 'configuration_test'],
            'php_fpm' => ['php_fpm', 'synthetic_pool_identity'],
            'caddy' => [str_ends_with($recipe, '_php_fpm') ? 'caddy' : 'frankenphp', 'caddyfile_adaptation'],
            'systemd_unit' => ['systemd', 'unit_verification'],
            'htaccess' => ['apache', 'request_rewrite_execution'],
        ];
        foreach ([false, true] as $requested) {
            $statuses = $requested ? array_fill_keys(array_keys($files), 'passed') : [];
            $report = ApplicationSyntaxReport::build($document, $requested, $statuses);
            self::assertSame(['schema','requested','coverage_complete','activation_authorized','runtime_validated','checks'], array_keys($report));
            self::assertSame('wcp360.candidate.application-syntax/1', $report['schema']);
            self::assertSame($requested, $report['requested']);
            foreach (['coverage_complete','activation_authorized','runtime_validated'] as $flag) self::assertFalse($report[$flag]);
            self::assertSame(array_keys($files), array_column($report['checks'], 'artifact'));
            self::assertSame(array_values($report['checks']), $report['checks']);
            foreach ($report['checks'] as $check) {
                self::assertSame(['artifact','sha256','tool','stage','status'], array_keys($check));
                $artifact = $check['artifact'];
                self::assertSame(hash('sha256', $files[$artifact]['content']), $check['sha256']);
                self::assertSame($stages[$artifact], [$check['tool'], $check['stage']]);
                self::assertSame($requested ? 'passed' : 'not_run', $check['status']);
                self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/D', $check['sha256']);
            }
        }
        self::assertSame($before, $document->fields());
    }

    public static function acceptedStatuses(): iterable
    {
        foreach (['passed','failed','not_configured','not_run'] as $status) yield $status => [$status];
    }

    #[DataProvider('acceptedStatuses')]
    public function testAcceptedStatusesRemainDistinctWithoutChangingQualification(string $status): void
    {
        $statuses = ['nginx' => $status, 'caddy' => 'not_configured', 'systemd_unit' => 'not_run'];
        $report = ApplicationSyntaxReport::build(self::document(), true, $statuses);
        self::assertSame(array_values($statuses), array_column($report['checks'], 'status'));
        self::assertFalse($report['coverage_complete']);
        self::assertFalse($report['activation_authorized']);
        self::assertFalse($report['runtime_validated']);
    }

    public static function rejectedStatusEnvelopes(): iterable
    {
        $base = self::statuses();
        yield 'requested without results' => [true, []];
        foreach (array_keys($base) as $artifact) {
            $missing = $base;
            unset($missing[$artifact]);
            yield 'missing '.$artifact => [true, $missing];
        }
        yield 'extra artifact' => [true, $base + ['apache' => 'passed']];
        yield 'wrong order' => [true, array_reverse($base, true)];
        yield 'numeric list' => [true, array_values($base)];
        yield 'unknown key' => [true, ['nginx' => 'passed', 'Caddy' => 'passed', 'systemd_unit' => 'passed']];
        yield 'not requested with supplied results' => [false, $base];
        foreach (['empty' => '', 'case' => 'PASSED', 'alias' => 'success', 'integer' => 1,
            'true' => true, 'false' => false, 'null' => null, 'array' => []] as $label => $value) {
            $invalid = $base;
            $invalid['caddy'] = $value;
            yield 'invalid status '.$label => [true, $invalid];
        }
    }

    #[DataProvider('rejectedStatusEnvelopes')]
    public function testMissingExtraReorderedOrUntypedResultsAreRefused(bool $requested, array $statuses): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationSyntaxReport::build(self::document(), $requested, $statuses);
    }

    public function testDigestBindsExactBytesIncludingTheFinalNewline(): void
    {
        $fields = self::fields('nginx_php_fpm', 'php');
        $fields['nginx'] = 'abc';
        $first = ApplicationPlanDocument::fromArray($fields, 'nginx_php_fpm', 'php');
        $fields['nginx'] = "abc\n";
        $second = ApplicationPlanDocument::fromArray($fields, 'nginx_php_fpm', 'php');
        $a = ApplicationSyntaxReport::build($first, false);
        $b = ApplicationSyntaxReport::build($second, false);
        self::assertSame('ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad', $a['checks'][0]['sha256']);
        self::assertSame('edeaaff3f1774ad2888673770c6d64097e391bc362d7d6fb34982ddf0efd18cb', $b['checks'][0]['sha256']);
        self::assertNotSame($a['checks'][0]['sha256'], $b['checks'][0]['sha256']);
        self::assertSame($a['checks'][1], $b['checks'][1]);
        self::assertSame('abc', $first->fields()['nginx']);
        self::assertSame("abc\n", $second->fields()['nginx']);
    }

    public function testReturnedReportsAreIndependentCopiesOfTheSameDocumentAndResults(): void
    {
        $document = self::document();
        $fields = $document->fields();
        $statuses = self::statuses();
        $expected = ApplicationSyntaxReport::build($document, true, $statuses);
        $changed = ApplicationSyntaxReport::build($document, true, $statuses);
        $changed['checks'][0]['status'] = 'failed';
        $changed['checks'][0]['sha256'] = str_repeat('0', 64);
        $changed['checks'][] = ['artifact' => 'neighbor'];
        $changed['activation_authorized'] = true;
        self::assertSame($expected, ApplicationSyntaxReport::build($document, true, $statuses));
        self::assertSame($fields, $document->fields());
        self::assertSame(self::statuses(), $statuses);
    }
}
