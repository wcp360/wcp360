<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationPlanRenderer;
use Wcp360\Core\Module\Hosting\Application\PreviewApplicationPlan;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ApplicationPlanPreviewTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function service(ApplicationPlanRenderer $renderer): PreviewApplicationPlan
    {
        return new PreviewApplicationPlan(new CandidateAuthorization($this->store->db), $renderer, self::getContainer()->get(\Wcp360\Core\Module\Hosting\Infrastructure\NativeApplicationPlanSyntax::class));
    }

    private function state(): array
    {
        $state = [];
        foreach (['sites','jobs','activations','audit','runtime_settings'] as $table) {
            $state[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        }
        return $state;
    }

    public function testActualGoSupportsEveryProfileAcrossSevenRecipesWithoutBusinessWrites(): void
    {
        $before = $this->state();
        $service = $this->service(self::getContainer()->get(GoPlanRenderer::class));
        $ids = [];
        foreach (ApplicationRecipeCatalogue::report()['recipes'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) {
                $report = $service->preview($this->fixture['users']['admin'], 'application.test', $recipe['id'], $application);
                self::assertSame($recipe['id'] === 'nginx_apache_php_fpm' ? 'wcp360.candidate.application-preview/2' : 'wcp360.candidate.application-preview/1', $report['schema']);
                self::assertSame($application, $report['application']);
                self::assertSame($recipe['id'], $report['recipe']);
                self::assertSame($recipe['web_server'], $report['web_server']);
                self::assertSame($recipe['application_server'], $report['application_server']);
                self::assertSame($recipe['htaccess_supported'], $report['htaccess_supported']);
                self::assertSame($recipe['artifacts'], array_keys($report['files']));
                self::assertSame('not_reserved', $report['listener_allocation']);
                foreach (['host_changes','persisted','deployment_available','performance_measured','worker_mode'] as $flag) self::assertFalse($report[$flag]);
                self::assertSame('disabled', $report['dynamic_page_cache']);
                self::assertSame('single_site_loopback', $report['scope']);
                self::assertSame('wcp360.candidate.application-syntax/1', $report['syntax_checks']['schema']);
                self::assertFalse($report['syntax_checks']['requested']);
                self::assertSame(array_fill(0, count($recipe['artifacts']), 'not_run'), array_column($report['syntax_checks']['checks'], 'status'));
                foreach (['coverage_complete','activation_authorized','runtime_validated'] as $flag) self::assertFalse($report['syntax_checks'][$flag]);
                $ids[] = $report['preview_site_id'];
            }
        }
        self::assertCount(21, array_unique($ids));
        self::assertSame($before, $this->state());
    }

    public function testUnsupportedNativeHtaccessAndUnknownInputsNeverRenderOrFallBack(): void
    {
        $before = $this->state();
        $renderer = new class implements ApplicationPlanRenderer {
            public function renderApplication(string $siteId, string $host, string $recipe, string $application): array { self::unreachable(); }
            private static function unreachable(): never { throw new \LogicException('Renderer must not be reached.'); }
        };
        $service = $this->service($renderer);
        foreach ([['example.com','nginx_php_fpm','php',false], ['app.test','unknown','php',false],
            ['app.test','nginx_php_fpm','worker',false], ['app.test','apache_frankenphp','wordpress',true],
            ['app.test','caddy_frankenphp','wordpress',true], ['example.com','nginx_apache_php_fpm','wordpress',true],
            ['app.test','nginx_apache_php_fpm','worker',true]] as [$host,$recipe,$application,$htaccess]) {
            try { $service->preview($this->fixture['users']['admin'], $host, $recipe, $application, $htaccess); self::fail('Invalid intent admitted.'); }
            catch (BadRequestHttpException $error) { self::assertSame(400, $error->getStatusCode()); }
        }
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id()] as $actor) {
            foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
                try { $service->preview($actor, 'app.test', $recipe); self::fail('Invalid actor admitted.'); }
                catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
            }
        }
        self::assertSame($before, $this->state());
    }

    public function testMalformedOrMissingRendererOutputIsRedactedWithoutWrites(): void
    {
        $before = $this->state();
        $renderer = new class implements ApplicationPlanRenderer {
            public function renderApplication(string $siteId, string $host, string $recipe, string $application): array
            {
                throw new \RuntimeException('private-application-driver-cause');
            }
        };
        foreach ([$renderer, new GoPlanRenderer('missing-application-binary')] as $port) {
            foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
                try { $this->service($port)->preview($this->fixture['users']['admin'], 'app.test', $recipe); self::fail('Renderer failure published.'); }
                catch (ServiceUnavailableHttpException $error) {
                    self::assertNull($error->getPrevious());
                    self::assertStringNotContainsString('private-application-driver-cause', (string) $error);
                }
            }
        }
        self::assertSame($before, $this->state());
    }

    public static function authorityRecipes(): iterable
    {
        yield 'existing version one' => ['nginx_php_fpm'];
        yield 'new version two' => ['nginx_apache_php_fpm'];
    }

    #[DataProvider('authorityRecipes')]
    public function testAuthorityRevokedDuringRenderingPreventsPublication(string $recipe): void
    {
        $store = $this->store;
        $actor = $this->fixture['users']['admin'];
        $driver = self::getContainer()->get(GoPlanRenderer::class);
        $renderer = new class($store, $actor, $driver) implements ApplicationPlanRenderer {
            public function __construct(private WorkbenchStore $store, private string $actor, private GoPlanRenderer $driver) {}
            public function renderApplication(string $siteId, string $host, string $recipe, string $application): array
            {
                $output = $this->driver->renderApplication($siteId, $host, $recipe, $application);
                $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->actor]);
                return $output;
            }
        };
        $this->expectException(AccessDeniedHttpException::class);
        $this->service($renderer)->preview($actor, 'app.test', $recipe);
    }

    public function testNativeHtaccessIntentIsAcceptedForDirectAndNginxFrontedApacheWithoutWrites(): void
    {
        $before = $this->state();
        $service = $this->service(self::getContainer()->get(GoPlanRenderer::class));
        foreach (['apache_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) {
                $report = $service->preview($this->fixture['users']['admin'], 'htaccess.test', $recipe, $application, true);
                self::assertTrue($report['htaccess_supported']);
                self::assertSame($recipe === 'nginx_apache_php_fpm' ? 'wcp360.candidate.application-preview/2' : 'wcp360.candidate.application-preview/1', $report['schema']);
                self::assertSame($recipe === 'nginx_apache_php_fpm' ? ['nginx','apache','php_fpm','htaccess'] : ['apache','php_fpm','htaccess'], array_keys($report['files']));
                self::assertNotSame('', $report['files']['htaccess']['content']);
                self::assertFalse($report['persisted']);
                self::assertFalse($report['host_changes']);
                self::assertFalse($report['deployment_available']);
                self::assertFalse($report['worker_mode']);
            }
        }
        self::assertSame($before, $this->state());
    }

    public static function invalidVersionTwoRendererOutput(): iterable
    {
        $base = ['schema' => 'wcp360.candidate.application-plan/2', 'application' => 'wordpress',
            'recipe' => 'nginx_apache_php_fpm', 'web_server' => 'nginx_apache', 'application_server' => 'php_fpm',
            'nginx' => "private-synthetic-nginx\n", 'apache' => "private-synthetic-apache\n",
            'php_fpm' => "private-synthetic-pool\n", 'htaccess' => "private-synthetic-htaccess\n"];
        yield 'downgraded schema' => [array_replace($base, ['schema' => 'wcp360.candidate.application-plan/1'])];
        yield 'apache only marker' => [array_replace($base, ['web_server' => 'apache'])];
        yield 'worker engine' => [array_replace($base, ['application_server' => 'frankenphp_worker'])];
        $missing = $base;
        unset($missing['apache']);
        yield 'missing middle webserver' => [$missing];
        yield 'extra activation claim' => [$base + ['deployment_available' => true]];
        yield 'changed field order' => [array_reverse($base, true)];
        yield 'malformed string' => [array_replace($base, ['htaccess' => "private\0htaccess"])];
    }

    #[DataProvider('invalidVersionTwoRendererOutput')]
    public function testMalformedVersionTwoDriverOutputIsRedactedWithoutWrites(array $fields): void
    {
        $before = $this->state();
        $renderer = new class($fields) implements ApplicationPlanRenderer {
            public function __construct(private array $fields) {}
            public function renderApplication(string $siteId, string $host, string $recipe, string $application): array
            {
                return $this->fields;
            }
        };
        try {
            $this->service($renderer)->preview($this->fixture['users']['admin'], 'app.test', 'nginx_apache_php_fpm', 'wordpress', true);
            self::fail('Malformed version two driver output was published.');
        } catch (ServiceUnavailableHttpException $error) {
            self::assertSame(503, $error->getStatusCode());
            self::assertSame('Application preview is unavailable. No installation was dispatched.', $error->getMessage());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('private-synthetic', (string) $error);
        }
        self::assertSame($before, $this->state());
    }
}
