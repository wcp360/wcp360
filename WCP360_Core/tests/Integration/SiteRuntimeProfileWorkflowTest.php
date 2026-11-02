<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Hosting\Application\PreparedPlanFiles;
use Wcp360\Core\Module\Hosting\Application\SubmitActivation;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Hosting\Domain\SiteRuntimeProfile;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\HostingPlanQueue;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class SiteRuntimeProfileWorkflowTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function submit(string $host = 'typed-profile.test', bool $worker = false, string $key = 'typed-profile-idempotency-2026'): string
    {
        $policy = new ApplicationServerPolicy(true);
        $input = ['tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => $host, 'idempotency_key' => $key];
        if ($worker) $input += ['application_server' => 'frankenphp_worker', 'worker_acknowledged' => true];
        $result = (new SubmitHostingPlan($this->store, $policy, new ApplicationServerSettings($this->store, $policy)))
            ->submit($this->fixture['users']['alice'], $input, str_repeat('c', 32));
        return $result['job_id'];
    }

    private function runPlan(): ?string
    {
        return (new RunHostingPlan($this->store, self::getContainer()->get(GoPlanRenderer::class), new ApplicationServerPolicy(true)))->runOne();
    }

    private function site(): array
    {
        return $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_sites');
    }

    private function job(string $id): array
    {
        return $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_jobs WHERE id=?', [$id]);
    }

    public function testInheritedAndExplicitRequestsPersistTheirOriginalTopologyAndKeepIdempotency(): void
    {
        $first = $this->submit();
        self::assertSame('classic', $this->site()['web_stack']);
        self::assertSame(1, (int) $this->site()['stack_revision']);
        $before = $this->job($first);
        $policy = new ApplicationServerPolicy(true);
        (new ApplicationServerSettings($this->store, $policy))->change($this->fixture['users']['admin'], 'frankenphp_worker', 1);
        self::assertSame($first, $this->submit());
        self::assertSame($before, $this->job($first));
        self::assertSame('classic', $this->site()['web_stack']);
        $worker = $this->submit('typed-worker.test', true, 'typed-worker-idempotency-2026');
        $row = $this->store->db->fetchAssociative('SELECT s.* FROM wcp_candidate_sites s JOIN wcp_candidate_jobs j ON s.id=j.site_id WHERE j.id=?', [$worker]);
        self::assertSame('advanced', $row['web_stack']);
        self::assertSame('frankenphp_worker', $row['application_server']);
        self::assertSame(1, (int) $row['stack_revision']);
    }

    public function testRealGoAndExportsRetainBothOriginalWireFormatsAndPublicProjection(): void
    {
        foreach ([false, true] as $worker) {
            $id = $this->submit($worker ? 'wire-advanced.test' : 'wire-classic.test', $worker,
                $worker ? 'wire-advanced-key-2026' : 'wire-classic-key-2026');
            self::assertSame($id, $this->runPlan());
            $job = $this->job($id);
            self::assertSame('SUCCEEDED', $job['state']);
            $document = json_decode($job['result'], true, flags: JSON_THROW_ON_ERROR);
            self::assertSame($worker ? 'wcp360.candidate.plan/2' : 'wcp360.candidate.plan/1', $document['schema']);
            self::assertSame($worker ? ['schema','nginx','application_server','caddy','systemd_unit'] : ['schema','nginx','php_fpm'], array_keys($document));
            $view = (new PreparedPlanFiles($this->store))->inspect($this->fixture['users']['alice'], $id);
            self::assertSame($worker ? ['nginx','caddy','systemd'] : ['nginx','php-fpm'], array_keys($view['files']));
            self::assertArrayNotHasKey('web_stack', $view['job']);
            self::assertArrayNotHasKey('stack_revision', $view['job']);
            self::assertArrayNotHasKey('site_id', $view['job']);
        }
    }

    public function testCallerCannotAssignReservedTopologyMetadataOrPersistModern(): void
    {
        $policy = new ApplicationServerPolicy(true);
        $service = new SubmitHostingPlan($this->store, $policy, new ApplicationServerSettings($this->store, $policy));
        foreach ([['web_stack' => 'classic'], ['web_stack' => 'modern'], ['web_stack' => 'legacy'], ['stack_revision' => 1]] as $extra) {
            try {
                $service->submit($this->fixture['users']['alice'], [
                    'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'forged-profile.test',
                    'idempotency_key' => 'forged-profile-key-2026',
                ] + $extra);
                self::fail('Caller selected reserved topology metadata.');
            } catch (BadRequestHttpException) {}
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
    }

    public function testChangedProfileAtPublicationDiscardsOldOutputWithOneCorrelatedFailure(): void
    {
        $id = $this->submit();
        $queue = new HostingPlanQueue($this->store);
        $claim = $queue->claim();
        $profile = SiteRuntimeProfile::fromStorage($this->site());
        $output = json_encode(self::getContainer()->get(GoPlanRenderer::class)->render($claim['site_id'], 'typed-profile.test'), JSON_THROW_ON_ERROR);
        $this->store->db->update('wcp_candidate_sites', ['application_server' => 'frankenphp_worker',
            'worker_acknowledged' => 1, 'web_stack' => 'advanced'], ['id' => $claim['site_id']]);
        self::assertTrue($queue->finish($id, $output, $profile));
        self::assertSame('FAILED', $this->job($id)['state']);
        self::assertNull($this->job($id)['result']);
        self::assertSame('PLAN_FAILED', $this->site()['state']);
        self::assertSame('advanced', $this->site()['web_stack']);
        self::assertSame(1, (int) $this->site()['stack_revision']);
        self::assertSame(['candidate.hosting.plan.queued','candidate.hosting.plan.started','candidate.hosting.plan.failed'],
            $this->store->db->fetchFirstColumn('SELECT kind FROM wcp_candidate_audit WHERE resource_id=? ORDER BY id', [$id]));
        self::assertSame([str_repeat('c',32),str_repeat('c',32),str_repeat('c',32)],
            $this->store->db->fetchFirstColumn('SELECT request_id FROM wcp_candidate_audit WHERE resource_id=? ORDER BY id', [$id]));
        self::assertFalse($queue->finish($id, $output, $profile));
    }

    public function testCorruptProfileBeforeRenderingCannotPublishAnOtherwiseValidGoPlan(): void
    {
        $id = $this->submit();
        $this->corruptProfile();
        self::assertSame($id, $this->runPlan());
        self::assertSame('FAILED', $this->job($id)['state']);
        self::assertNull($this->job($id)['result']);
        self::assertSame('PLAN_FAILED', $this->site()['state']);
    }

    public function testCorruptSuccessfulProfileCannotBePreviewedOrDownloadedAndIsRedacted(): void
    {
        $id = $this->submit();
        $this->runPlan();
        $result = $this->job($id)['result'];
        $this->corruptProfile();
        $files = new PreparedPlanFiles($this->store);
        foreach ([false, true] as $download) {
            try {
                if ($download) $files->download($this->fixture['users']['alice'], $id, 'nginx');
                else $files->inspect($this->fixture['users']['alice'], $id);
                self::fail('Corrupt profile exported configuration.');
            } catch (ServiceUnavailableHttpException $error) {
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-profile-corruption', (string) $error);
            }
        }
        self::assertSame($result, $this->job($id)['result']);
        self::assertSame('SUCCEEDED', $this->job($id)['state']);
    }

    public function testClassicActivationCannotBeQueuedWithInconsistentPersistedTopology(): void
    {
        $id = $this->submit();
        $this->runPlan();
        $this->store->db->insert('wcp_candidate_activation_grants', ['user_id' => $this->fixture['users']['alice'],
            'tenant_id' => $this->fixture['tenants']['alice']]);
        $this->corruptProfile();
        try {
            (new SubmitActivation($this->store, true))->submit($this->fixture['users']['alice'], $id);
            self::fail('Activation accepted inconsistent topology.');
        } catch (ConflictHttpException) {}
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_activations'));
    }

    public function testSQLiteRunsTheSameTypedProfileAndGuardedPublicationFlow(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $this->store = new WorkbenchStore($db);
            $this->fixture = WorkbenchFixture::reset($db);
            $id = $this->submit('sqlite-profile.test', true);
            self::assertSame($id, $this->runPlan());
            self::assertSame('SUCCEEDED', $this->job($id)['state']);
            self::assertSame('advanced', $this->site()['web_stack']);
            self::assertSame(1, (int) $this->site()['stack_revision']);
            self::assertSame(['nginx','caddy','systemd'],
                array_keys((new PreparedPlanFiles($this->store))->inspect($this->fixture['users']['alice'], $id)['files']));
        } finally {
            $db->close();
        }
    }

    private function corruptProfile(): void
    {
        // Simulate damaged/restored metadata only in the disposable test database.
        if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->store->db->executeStatement('DROP TRIGGER wcp_candidate_site_runtime_insert');
            $this->store->db->executeStatement('DROP TRIGGER wcp_candidate_site_runtime_update');
        } else {
            $this->store->db->executeStatement('ALTER TABLE wcp_candidate_sites DROP CONSTRAINT wcp_candidate_site_runtime_profile');
        }
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET web_stack='private-profile-corruption'");
    }
}
