<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Process\Process;
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
        return new PreviewApplicationPlan(new CandidateAuthorization($this->store->db), $renderer);
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
        $renderer = new class implements ApplicationPlanRenderer {
            public function renderApplication(string $siteId, string $host, string $recipe, string $application): array { self::unreachable(); }
            private static function unreachable(): never { throw new \LogicException('Renderer must not be reached.'); }
        };
        $service = $this->service($renderer);
        foreach ([['example.com','nginx_php_fpm','php',false], ['app.test','unknown','php',false],
            ['app.test','nginx_php_fpm','worker',false], ['app.test','apache_frankenphp','wordpress',true],
            ['app.test','caddy_frankenphp','wordpress',true]] as [$host,$recipe,$application,$htaccess]) {
            try { $service->preview($this->fixture['users']['admin'], $host, $recipe, $application, $htaccess); self::fail('Invalid intent admitted.'); }
            catch (BadRequestHttpException) {}
        }
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id()] as $actor) {
            try { $service->preview($actor, 'app.test', 'nginx_php_fpm'); self::fail('Invalid actor admitted.'); }
            catch (AccessDeniedHttpException) {}
        }
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

    public static function nginxRecipes(): iterable
    {
        foreach (['nginx_php_fpm','nginx_frankenphp'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) yield $recipe.'/'.$application => [$recipe,$application];
        }
    }

    #[DataProvider('nginxRecipes')]
    public function testGeneratedNginxConfigurationParsesInDisposableLinuxCi(string $recipe, string $application): void
    {
        if (PHP_OS_FAMILY !== 'Linux' || !is_file('/usr/sbin/nginx')) self::markTestSkipped('Native Nginx syntax verification needs the Linux CI image.');
        $dir = sys_get_temp_dir().'/wcp-application-nginx-'.bin2hex(random_bytes(8));
        mkdir($dir, 0700);
        try {
            $plan = self::getContainer()->get(GoPlanRenderer::class)->renderApplication(WorkbenchStore::id(), 'syntax.test', $recipe, $application);
            file_put_contents($dir.'/site.conf', $plan['nginx']);
            $http = "access_log off; client_body_temp_path $dir/body; proxy_temp_path $dir/proxy; fastcgi_temp_path $dir/fastcgi; uwsgi_temp_path $dir/uwsgi; scgi_temp_path $dir/scgi; include $dir/site.conf;";
            file_put_contents($dir.'/nginx.conf', "pid $dir/nginx.pid; error_log stderr; events { worker_connections 8; } http { $http }");
            $process = new Process(['/usr/sbin/nginx','-t','-p',$dir.'/','-c',$dir.'/nginx.conf'], timeout: 5);
            $process->run();
            self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        } finally {
            foreach (['site.conf','nginx.conf','nginx.pid'] as $name) if (is_file($dir.'/'.$name)) unlink($dir.'/'.$name);
            foreach (['body','proxy','fastcgi','uwsgi','scgi'] as $name) if (is_dir($dir.'/'.$name)) rmdir($dir.'/'.$name);
            rmdir($dir);
        }
    }
}
