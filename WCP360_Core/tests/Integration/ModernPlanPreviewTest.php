<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Hosting\Application\ModernPlanRenderer;
use Wcp360\Core\Module\Hosting\Application\PreviewModernHostingPlan;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ModernPlanPreviewTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    public function testActualGoDriverEmitsOnlyModernArtifactsAndChangesNoBusinessState(): void
    {
        $before = $this->snapshot();
        $preview = $this->service(self::getContainer()->get(GoPlanRenderer::class));
        $first = $preview->preview($this->fixture['users']['admin'], 'direct-modern.test', true);
        $second = $preview->preview($this->fixture['users']['admin'], 'direct-modern.test', true);
        self::assertSame('wcp360.candidate.modern-preview/1', $first['schema']);
        self::assertSame('modern', $first['web_stack']);
        self::assertSame('single_site_loopback', $first['scope']);
        foreach (['host_changes','persisted','activation_available'] as $flag) self::assertFalse($first[$flag]);
        self::assertSame('not_reserved', $first['listener_allocation']);
        self::assertTrue(Uuid::isValid($first['preview_site_id']));
        self::assertSame('7', $first['preview_site_id'][14]);
        self::assertNotSame($first['preview_site_id'], $second['preview_site_id']);
        self::assertSame(['caddy','systemd'], array_keys($first['files']));
        self::assertStringContainsString('bind 127.0.0.1', $first['files']['caddy']['content']);
        self::assertStringContainsString('http://direct-modern.test:8082', $first['files']['caddy']['content']);
        self::assertStringContainsString('admin off', $first['files']['caddy']['content']);
        self::assertStringContainsString('auto_https off', $first['files']['caddy']['content']);
        self::assertStringNotContainsString('proxy_pass', serialize($first['files']));
        self::assertStringNotContainsString('User=root', $first['files']['systemd']['content']);
        self::assertSame($before, $this->snapshot());
    }

    public function testExistingRenderMethodsStillHaveTheirOriginalWireAndArtifacts(): void
    {
        $renderer = self::getContainer()->get(GoPlanRenderer::class);
        $id = WorkbenchStore::id();
        $fpm = $renderer->render($id, 'legacy-modern-boundary.test');
        self::assertSame(['schema','nginx','php_fpm'], array_keys($fpm));
        self::assertSame('wcp360.candidate.plan/1', $fpm['schema']);
        $advanced = $renderer->render($id, 'advanced-modern-boundary.test', ApplicationServer::FrankenPhpWorker, true);
        self::assertSame(['schema','nginx','application_server','caddy','systemd_unit'], array_keys($advanced));
        self::assertSame('wcp360.candidate.plan/2', $advanced['schema']);
        self::assertSame(['nginx','caddy','systemd'], array_keys(PreparedPlanDocument::fromArray($advanced, ApplicationServer::FrankenPhpWorker)->files()));
    }

    public function testUserMissingInactiveRootAndDisabledPlanningCannotReachRenderer(): void
    {
        $renderer = $this->probe(static function (): array { self::fail('Unauthorized rendering.'); });
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id(), 'invalid'] as $actor) {
            try { $this->service($renderer)->preview($actor, 'modern.test', true); self::fail('Unauthorized preview.'); }
            catch (AccessDeniedHttpException) {}
        }
        try { $this->service($renderer, false)->preview($this->fixture['users']['admin'], 'modern.test', true); self::fail('Worker opt-in bypassed.'); }
        catch (AccessDeniedHttpException) {}
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->expectException(AccessDeniedHttpException::class);
        $this->service($renderer)->preview($this->fixture['users']['admin'], 'modern.test', true);
    }

    public function testBadHostnamesAndMissingAcknowledgementRefuseBeforeRendering(): void
    {
        $renderer = $this->probe(static function (): array { self::fail('Invalid rendering request.'); });
        foreach ([['example.com',true], ['UPPER.test',true], ["bad.test\nadmin on",true],
            ['../bad.test',true], ['modern.test',false], [str_repeat('a', 254).'.test',true]] as [$host,$ack]) {
            try { $this->service($renderer)->preview($this->fixture['users']['admin'], $host, $ack); self::fail('Invalid input accepted.'); }
            catch (BadRequestHttpException) {}
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
    }

    public function testRendererFailureAndMalformedResultsAreRedactedWithoutSideEffects(): void
    {
        $before = $this->snapshot();
        foreach ([
            $this->probe(static function (): array { throw new \RuntimeException('private-modern-renderer-marker'); }),
            $this->probe(static fn (): array => ['schema' => 'wcp360.candidate.plan/2','nginx' => 'private-modern-renderer-marker']),
            new GoPlanRenderer('nonexistent-modern-preview-binary'),
        ] as $renderer) {
            try { $this->service($renderer)->preview($this->fixture['users']['admin'], 'modern.test', true); self::fail('Invalid preview published.'); }
            catch (ServiceUnavailableHttpException $error) {
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-modern-renderer-marker', (string) $error);
            }
        }
        self::assertSame($before, $this->snapshot());
    }

    public function testRootRevocationDuringRenderingPreventsPublication(): void
    {
        $store = $this->store;
        $actor = $this->fixture['users']['admin'];
        $renderer = $this->probe(static function () use ($store,$actor): array {
            $store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $actor]);
            return ['schema' => 'wcp360.candidate.plan/3','web_stack' => 'modern',
                'application_server' => 'frankenphp_worker','caddy' => 'synthetic','systemd_unit' => 'synthetic'];
        });
        $this->expectException(AccessDeniedHttpException::class);
        $this->service($renderer)->preview($actor, 'modern.test', true);
    }

    private function service(ModernPlanRenderer $renderer, bool $enabled = true): PreviewModernHostingPlan
    {
        return new PreviewModernHostingPlan(new CandidateAuthorization($this->store->db), new ApplicationServerPolicy($enabled), $renderer);
    }

    private function probe(\Closure $result): ModernPlanRenderer
    {
        return new class($result) implements ModernPlanRenderer {
            public function __construct(private \Closure $result) {}
            public function renderModern(string $siteId, string $host, bool $acknowledged): array { return ($this->result)(); }
        };
    }

    private function snapshot(): array
    {
        $rows = [];
        foreach (['sites','jobs','activations','audit','runtime_settings'] as $table) {
            $rows[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        }
        return $rows;
    }
}
