<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

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

    public function testActualGoSupportsEveryProfileAcrossSixRecipesWithoutBusinessWrites(): void
    {
        $before = $this->state();
        $service = $this->service(self::getContainer()->get(GoPlanRenderer::class));
        $ids = [];
        foreach (ApplicationRecipeCatalogue::report()['recipes'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) {
                $report = $service->preview($this->fixture['users']['admin'], 'application.test', $recipe['id'], $application);
                self::assertSame($application, $report['application']);
                self::assertSame($recipe['artifacts'], array_keys($report['files']));
                self::assertSame('not_reserved', $report['listener_allocation']);
                foreach (['host_changes','persisted','deployment_available','performance_measured','worker_mode'] as $flag) self::assertFalse($report[$flag]);
                self::assertSame('disabled', $report['dynamic_page_cache']);
                $ids[] = $report['preview_site_id'];
            }
        }
        self::assertCount(18, array_unique($ids));
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
            ['app.test','caddy_frankenphp','wordpress',true]] as [$host,$recipe,$application,$htaccess]) {
            try { $service->preview($this->fixture['users']['admin'], $host, $recipe, $application, $htaccess); self::fail('Invalid intent admitted.'); }
            catch (BadRequestHttpException $error) { self::assertSame(400, $error->getStatusCode()); }
        }
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id()] as $actor) {
            try { $service->preview($actor, 'app.test', 'nginx_php_fpm'); self::fail('Invalid actor admitted.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
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
            try { $this->service($port)->preview($this->fixture['users']['admin'], 'app.test', 'nginx_php_fpm'); self::fail('Renderer failure published.'); }
            catch (ServiceUnavailableHttpException $error) {
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-application-driver-cause', (string) $error);
            }
        }
        self::assertSame($before, $this->state());
    }

    public function testAuthorityRevokedDuringRenderingPreventsPublication(): void
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
        $this->service($renderer)->preview($actor, 'app.test', 'nginx_php_fpm');
    }

}
