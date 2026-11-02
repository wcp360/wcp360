<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;
use Wcp360\Core\Shared\Platform\WebStackCatalogue;

final class ApplicationPlanDocumentTest extends TestCase
{
    public static function combinations(): iterable
    {
        foreach (ApplicationRecipeCatalogue::report()['recipes'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) yield $recipe['id'].'/'.$application => [$recipe['id'], $application];
        }
    }

    private static function fields(string $recipe = 'nginx_php_fpm', string $application = 'php'): array
    {
        $choice = ApplicationRecipeCatalogue::choice($recipe);
        $fields = ['schema' => ApplicationRecipeCatalogue::planSchema($recipe), 'application' => $application, 'recipe' => $recipe,
            'web_server' => $choice['web_server'], 'application_server' => $choice['application_server']];
        foreach ($choice['artifacts'] as $key) $fields[$key] = "synthetic\n";
        return $fields;
    }

    #[DataProvider('combinations')]
    public function testEveryRecipeAndApplicationRetainsExactEnvelopeAndArtifactBytes(string $recipe, string $application): void
    {
        $fields = self::fields($recipe, $application);
        $document = ApplicationPlanDocument::decode(json_encode($fields, JSON_THROW_ON_ERROR), $recipe, $application);
        self::assertSame($fields, $document->fields());
        self::assertSame(ApplicationRecipeCatalogue::choice($recipe)['artifacts'], array_keys($document->files()));
        foreach ($document->files() as $key => $file) self::assertSame($fields[$key], $file['content']);
    }

    public function testCatalogueDoesNotPromoteDeploymentPerformanceOrExistingStackSelection(): void
    {
        $before = WebStackCatalogue::report(true);
        $report = ApplicationRecipeCatalogue::report();
        self::assertSame('wcp360.candidate.application-recipes/2', $report['schema']);
        self::assertCount(7, $report['recipes']);
        self::assertSame(['php','front_controller','wordpress'], array_column($report['profiles'], 'id'));
        foreach (['deployment_available','persisted','performance_measured'] as $flag) self::assertFalse($report[$flag]);
        self::assertSame(['apache_php_fpm','nginx_apache_php_fpm'], array_column(array_filter($report['recipes'], static fn (array $r): bool => $r['htaccess_supported']), 'id'));
        self::assertSame($before, WebStackCatalogue::report(true));
    }

    public static function boundedRecipes(): iterable
    {
        yield 'existing version one' => ['nginx_php_fpm'];
        yield 'new version two' => ['nginx_apache_php_fpm'];
    }

    #[DataProvider('boundedRecipes')]
    public function testDecodedAndJsonByteLimitsRemainSharedWithExistingDocuments(string $recipe): void
    {
        $fields = self::fields($recipe);
        $fields['nginx'] = str_repeat('/', PreparedPlanDocument::MAX_CONTENT_BYTES - array_sum(array_map(strlen(...), $fields)) + strlen($fields['nginx']));
        self::assertSame(PreparedPlanDocument::MAX_CONTENT_BYTES, array_sum(array_map(strlen(...), $fields)));
        self::assertSame($fields, ApplicationPlanDocument::fromArray($fields, $recipe, 'php')->fields());
        $fields['nginx'] .= 'x';
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::fromArray($fields, $recipe, 'php');
    }

    public static function invalidFields(): iterable
    {
        $base = self::fields();
        yield 'old schema' => [array_replace($base, ['schema' => 'wcp360.candidate.plan/1'])];
        yield 'wrong profile' => [array_replace($base, ['application' => 'wordpress'])];
        yield 'wrong recipe' => [array_replace($base, ['recipe' => 'apache_php_fpm'])];
        yield 'wrong web server' => [array_replace($base, ['web_server' => 'caddy'])];
        yield 'worker engine' => [array_replace($base, ['application_server' => 'frankenphp_worker'])];
        yield 'wrong order' => [array_reverse($base, true)];
        yield 'extra artifact' => [$base + ['apache' => 'synthetic']];
        yield 'empty artifact' => [array_replace($base, ['nginx' => ''])];
        yield 'control' => [array_replace($base, ['nginx' => "private\0input"])];
        yield 'bad UTF8' => [array_replace($base, ['nginx' => "\xff"])];
        yield 'array content' => [array_replace($base, ['nginx' => ['private']])];
        yield 'null content' => [array_replace($base, ['nginx' => null])];
        foreach (array_keys($base) as $key) { $bad = $base; unset($bad[$key]); yield 'missing '.$key => [$bad]; }
    }

    #[DataProvider('invalidFields')]
    public function testMalformedCrossProfileOrUnboundedArtifactsAreRefused(array $fields): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::fromArray($fields, 'nginx_php_fpm', 'php');
    }

    public function testUnknownProfileAndRecipeCannotBecomeConfiguration(): void
    {
        foreach ([['unknown','php'],['nginx_php_fpm','worker']] as [$recipe,$application]) {
            try { ApplicationPlanDocument::fromArray(self::fields(), $recipe, $application); self::fail('Unknown selector admitted.'); }
            catch (\InvalidArgumentException) {}
        }
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::fromArray(self::fields(), ApplicationServer::PhpFpm);
    }

    #[DataProvider('boundedRecipes')]
    public function testOversizedJsonIsRefusedBeforeParsing(string $recipe): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::decode(str_repeat(' ', PreparedPlanDocument::MAX_JSON_BYTES + 1), $recipe, 'php');
    }

    public static function legacyChoices(): iterable
    {
        yield 'nginx fpm' => [['id' => 'nginx_php_fpm', 'label' => 'Nginx + PHP-FPM',
            'web_server' => 'nginx', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
            'artifacts' => ['nginx', 'php_fpm']]];
        yield 'nginx frankenphp' => [['id' => 'nginx_frankenphp', 'label' => 'Nginx + FrankenPHP classic',
            'web_server' => 'nginx', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
            'artifacts' => ['nginx', 'caddy', 'systemd_unit']]];
        yield 'apache fpm' => [['id' => 'apache_php_fpm', 'label' => 'Apache + PHP-FPM',
            'web_server' => 'apache', 'application_server' => 'php_fpm', 'htaccess_supported' => true,
            'artifacts' => ['apache', 'php_fpm', 'htaccess']]];
        yield 'apache frankenphp' => [['id' => 'apache_frankenphp', 'label' => 'Apache + FrankenPHP classic',
            'web_server' => 'apache', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
            'artifacts' => ['apache', 'caddy', 'systemd_unit']]];
        yield 'caddy fpm' => [['id' => 'caddy_php_fpm', 'label' => 'Caddy + PHP-FPM',
            'web_server' => 'caddy', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
            'artifacts' => ['caddy', 'php_fpm']]];
        yield 'caddy frankenphp' => [['id' => 'caddy_frankenphp', 'label' => 'Caddy / FrankenPHP classic (direct)',
            'web_server' => 'caddy', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
            'artifacts' => ['caddy', 'systemd_unit']]];
    }

    #[DataProvider('legacyChoices')]
    public function testSixExistingChoicesRetainTheirMetadataAndVersionOneDocumentBytes(array $expected): void
    {
        $recipe = $expected['id'];
        self::assertSame($expected + ['plan_schema' => 'wcp360.candidate.application-plan/1'], ApplicationRecipeCatalogue::choice($recipe));
        self::assertSame('wcp360.candidate.application-plan/1', ApplicationRecipeCatalogue::planSchema($recipe));
        $fields = ['schema' => 'wcp360.candidate.application-plan/1', 'application' => 'wordpress',
            'recipe' => $recipe, 'web_server' => $expected['web_server'], 'application_server' => $expected['application_server']];
        foreach ($expected['artifacts'] as $artifact) $fields[$artifact] = "original:$artifact\r\n\t# /literal; valeur é\n";
        $bytes = json_encode($fields, JSON_THROW_ON_ERROR);
        $document = ApplicationPlanDocument::decode($bytes, $recipe, 'wordpress');
        self::assertSame($fields, $document->fields());
        self::assertSame($bytes, json_encode($document->fields(), JSON_THROW_ON_ERROR));
        foreach ($document->files() as $artifact => $file) self::assertSame($fields[$artifact], $file['content']);
    }

    public static function newApplications(): iterable
    {
        foreach (['php','front_controller','wordpress'] as $application) yield $application => [$application];
    }

    #[DataProvider('newApplications')]
    public function testNewChainRequiresItsVersionTwoMarkersAndFourOrderedArtifacts(string $application): void
    {
        $fields = ['schema' => 'wcp360.candidate.application-plan/2', 'application' => $application,
            'recipe' => 'nginx_apache_php_fpm', 'web_server' => 'nginx_apache', 'application_server' => 'php_fpm',
            'nginx' => "nginx\r\n", 'apache' => "apache\n", 'php_fpm' => "pool\tvalue\n", 'htaccess' => "rewrite é\n"];
        $document = ApplicationPlanDocument::decode(json_encode($fields, JSON_THROW_ON_ERROR), 'nginx_apache_php_fpm', $application);
        self::assertSame($fields, $document->fields());
        self::assertSame(['schema','application','recipe','web_server','application_server','nginx','apache','php_fpm','htaccess'], array_keys($document->fields()));
        self::assertSame(['nginx','apache','php_fpm','htaccess'], array_keys($document->files()));
        self::assertSame(['nginx-application.conf','apache-application.conf','php-fpm-application.conf','.htaccess'], array_column($document->files(), 'name'));
        self::assertSame('wcp360.candidate.application-plan/2', ApplicationRecipeCatalogue::planSchema('nginx_apache_php_fpm'));
        foreach ($document->files() as $artifact => $file) self::assertSame($fields[$artifact], $file['content']);
    }

    #[DataProvider('combinations')]
    public function testPlanVersionsCannotBeDowngradedOrPromotedAcrossRecipes(string $recipe, string $application): void
    {
        $fields = self::fields($recipe, $application);
        $fields['schema'] = $recipe === 'nginx_apache_php_fpm' ? 'wcp360.candidate.application-plan/1' : 'wcp360.candidate.application-plan/2';
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::decode(json_encode($fields, JSON_THROW_ON_ERROR), $recipe, $application);
    }

    public static function invalidNewFields(): iterable
    {
        $base = self::fields('nginx_apache_php_fpm', 'wordpress');
        yield 'old schema' => [array_replace($base, ['schema' => 'wcp360.candidate.application-plan/1'])];
        yield 'unversioned schema' => [array_replace($base, ['schema' => 'wcp360.candidate.application-plan'])];
        yield 'wrong profile' => [array_replace($base, ['application' => 'php'])];
        yield 'old apache recipe' => [array_replace($base, ['recipe' => 'apache_php_fpm'])];
        yield 'apache only marker' => [array_replace($base, ['web_server' => 'apache'])];
        yield 'nginx only marker' => [array_replace($base, ['web_server' => 'nginx'])];
        yield 'classic franken engine' => [array_replace($base, ['application_server' => 'frankenphp_classic'])];
        yield 'worker engine' => [array_replace($base, ['application_server' => 'frankenphp_worker'])];
        yield 'reordered fields' => [array_reverse($base, true)];
        $reordered = $base;
        unset($reordered['apache']);
        $reordered['apache'] = $base['apache'];
        yield 'apache artifact moved' => [$reordered];
        yield 'extra caddy artifact' => [$base + ['caddy' => 'synthetic']];
        yield 'deployment flag' => [$base + ['deployment_available' => true]];
        yield 'empty apache artifact' => [array_replace($base, ['apache' => ''])];
        yield 'control htaccess' => [array_replace($base, ['htaccess' => "rewrite\0private"])];
        yield 'invalid UTF8 artifact' => [array_replace($base, ['apache' => "\xff"])];
        yield 'untyped pool' => [array_replace($base, ['php_fpm' => ['private']])];
        foreach (array_keys($base) as $key) {
            $missing = $base;
            unset($missing[$key]);
            yield 'missing '.$key => [$missing];
        }
    }

    #[DataProvider('invalidNewFields')]
    public function testMalformedVersionTwoFieldsNeverBecomeAPlan(array $fields): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::fromArray($fields, 'nginx_apache_php_fpm', 'wordpress');
    }

    public static function malformedNewJson(): iterable
    {
        yield 'truncated' => ['{"schema":"wcp360.candidate.application-plan/2"', \JsonException::class];
        yield 'invalid JSON UTF8' => ["{\"schema\":\"\xff\"}", \JsonException::class];
        yield 'scalar' => ['"private"', \InvalidArgumentException::class];
        yield 'list' => ['["private"]', \InvalidArgumentException::class];
        yield 'null' => ['null', \InvalidArgumentException::class];
    }

    #[DataProvider('malformedNewJson')]
    public function testMalformedVersionTwoJsonIsRejected(string $json, string $exception): void
    {
        $this->expectException($exception);
        ApplicationPlanDocument::decode($json, 'nginx_apache_php_fpm', 'wordpress');
    }

    public function testSchemaLookupRejectsAnUnknownRecipe(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationRecipeCatalogue::planSchema('private-unknown-recipe');
    }
}
