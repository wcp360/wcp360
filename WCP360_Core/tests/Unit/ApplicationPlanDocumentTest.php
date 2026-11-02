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
        $fields = ['schema' => 'wcp360.candidate.application-plan/1', 'application' => $application, 'recipe' => $recipe,
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
        self::assertCount(6, $report['recipes']);
        self::assertSame(['php','front_controller','wordpress'], array_column($report['profiles'], 'id'));
        foreach (['deployment_available','persisted','performance_measured'] as $flag) self::assertFalse($report[$flag]);
        self::assertSame(['apache_php_fpm'], array_column(array_filter($report['recipes'], static fn (array $r): bool => $r['htaccess_supported']), 'id'));
        self::assertSame($before, WebStackCatalogue::report(true));
    }

    public function testDecodedAndJsonByteLimitsRemainSharedWithExistingDocuments(): void
    {
        $fields = self::fields();
        $fields['nginx'] = str_repeat('/', PreparedPlanDocument::MAX_CONTENT_BYTES - array_sum(array_map(strlen(...), $fields)) + strlen($fields['nginx']));
        self::assertSame(PreparedPlanDocument::MAX_CONTENT_BYTES, array_sum(array_map(strlen(...), $fields)));
        self::assertSame($fields, ApplicationPlanDocument::fromArray($fields, 'nginx_php_fpm', 'php')->fields());
        $fields['nginx'] .= 'x';
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::fromArray($fields, 'nginx_php_fpm', 'php');
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

    public function testOversizedJsonIsRefusedBeforeParsing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationPlanDocument::decode(str_repeat(' ', PreparedPlanDocument::MAX_JSON_BYTES + 1), 'nginx_php_fpm', 'php');
    }
}
