<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Hosting\Application\PreparedPlanFiles;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PreparedPlanFilesTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private PreparedPlanFiles $files;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->files = new PreparedPlanFiles($this->store);
    }

    private function submit(bool $worker = false): string
    {
        $policy = new ApplicationServerPolicy(true);
        $submit = new SubmitHostingPlan($this->store, $policy, new ApplicationServerSettings($this->store, $policy));
        return $submit->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'prepared-files.test',
            'idempotency_key' => 'prepared-file-key-2026', 'application_server' => $worker ? 'frankenphp_worker' : 'php_fpm',
            'worker_acknowledged' => $worker,
        ])['job_id'];
    }

    private function render(bool $worker = false): string
    {
        $id = $this->submit($worker);
        (new RunHostingPlan($this->store, self::getContainer()->get(GoPlanRenderer::class), new ApplicationServerPolicy(true)))->runOne();
        return $id;
    }

    public function testRenderedFpmExportsExactBytesWithNoBusinessWritesOrPrivateMetadata(): void
    {
        $id = $this->render();
        $original = json_decode($this->store->db->fetchOne('SELECT result FROM wcp_candidate_jobs WHERE id=?', [$id]), true, flags: JSON_THROW_ON_ERROR);
        $jobs = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id');
        $sites = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id');
        $audit = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $view = $this->files->inspect($this->fixture['users']['alice'], $id);
        self::assertSame(['nginx', 'php-fpm'], array_keys($view['files']));
        foreach (['result', 'fingerprint', 'idempotency_key', 'actor_id', 'site_id'] as $private) self::assertArrayNotHasKey($private, $view['job']);
        foreach (['nginx' => 'nginx', 'php-fpm' => 'php_fpm'] as $artifact => $field) {
            $file = $this->files->download($this->fixture['users']['alice'], $id, $artifact);
            self::assertSame($original[$field], $file['content']);
            self::assertStringStartsWith('wcp360-'.$id.'-', $file['name']);
            self::assertMatchesRegularExpression('/\A[a-zA-Z0-9.-]+\z/D', $file['name']);
        }
        self::assertSame($jobs, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id'));
        self::assertSame($sites, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id'));
        self::assertSame($audit, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testStoredWorkerPlanExportsItsOwnArtifactsWithoutGrantingNewActivation(): void
    {
        $id = $this->render(true);
        self::assertSame(['nginx', 'caddy', 'systemd'], array_keys($this->files->inspect($this->fixture['users']['alice'], $id)['files']));
        self::assertStringContainsString('[Service]', $this->files->download($this->fixture['users']['alice'], $id, 'systemd')['content']);
        $this->expectException(NotFoundHttpException::class);
        $this->files->download($this->fixture['users']['alice'], $id, 'php-fpm');
    }

    public static function unfinished(): iterable
    {
        foreach (['QUEUED', 'RUNNING', 'FAILED', 'UNKNOWN_OUTCOME'] as $state) yield [$state];
    }

    #[DataProvider('unfinished')]
    public function testNonSuccessfulTasksCannotExportEvenIfAResultIsPresent(string $state): void
    {
        $id = $this->submit();
        $this->store->db->update('wcp_candidate_jobs', ['state' => $state, 'result' => 'private-stale-result'], ['id' => $id]);
        self::assertSame([], $this->files->inspect($this->fixture['users']['alice'], $id)['files']);
        $this->expectException(ConflictHttpException::class);
        $this->files->download($this->fixture['users']['alice'], $id, 'nginx');
    }

    public function testInvalidArtifactAndMalformedOrMissingIdentityHaveNoFilesystemFallback(): void
    {
        foreach (['../nginx', '/etc/passwd', "nginx\r\nX-Test: injected", 'php_fpm'] as $artifact) {
            try { $this->files->download($this->fixture['users']['alice'], WorkbenchStore::id(), $artifact); self::fail('Invalid artifact admitted.'); }
            catch (NotFoundHttpException $error) { self::assertSame(404, $error->getStatusCode()); }
        }
        foreach (['private-invalid-id', WorkbenchStore::id()] as $id) {
            try { $this->files->inspect($this->fixture['users']['alice'], $id); self::fail('Invalid job admitted.'); }
            catch (NotFoundHttpException $error) { self::assertSame(404, $error->getStatusCode()); }
        }
    }

    public function testAuthorityIsCheckedBeforeDecodingAnotherTenantsCorruptPlan(): void
    {
        $id = $this->submit();
        $this->store->db->update('wcp_candidate_jobs', ['state' => 'SUCCEEDED', 'result' => 'private-corrupt-result'], ['id' => $id]);
        foreach (['bob', 'admin'] as $name) {
            try { $this->files->inspect($this->fixture['users'][$name], $id); self::fail('Foreign document inspected.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
    }

    public function testCurrentAccessBlockAndInactivationRevokeAnExistingDownload(): void
    {
        $id = $this->render();
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied(
            $this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        try { $this->files->download($this->fixture['users']['alice'], $id, 'nginx'); self::fail('Blocked download admitted.'); }
        catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        $this->store->db->delete('wcp_candidate_permission_denials', ['user_id' => $this->fixture['users']['alice']]);
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['alice']]);
        $this->expectException(AccessDeniedHttpException::class);
        $this->files->download($this->fixture['users']['alice'], $id, 'nginx');
    }

    public static function corrupt(): iterable
    {
        yield [null];
        yield ['private-malformed-json'];
        yield ['{"schema":"private-unknown-schema","nginx":"x","php_fpm":"x"}'];
        yield [str_repeat('x', 393217)];
    }

    #[DataProvider('corrupt')]
    public function testMalformedSuccessFailsWithGenericUnavailableMessage(?string $result): void
    {
        $id = $this->submit();
        $this->store->db->update('wcp_candidate_jobs', ['state' => 'SUCCEEDED', 'result' => $result], ['id' => $id]);
        try { $this->files->download($this->fixture['users']['alice'], $id, 'nginx'); self::fail('Corrupt plan exported.'); }
        catch (ServiceUnavailableHttpException $error) {
            self::assertSame(503, $error->getStatusCode());
            self::assertSame('Prepared plan files are unavailable.', $error->getMessage());
            self::assertNull($error->getPrevious());
        }
    }
}
