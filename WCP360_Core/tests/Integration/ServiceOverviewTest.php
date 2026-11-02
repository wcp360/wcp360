<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ServiceObservation;
use Wcp360\Core\Module\Platform\Application\ServiceOverview;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ServiceOverviewTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private static function block(string $id, string $load = 'loaded', string $active = 'active', string $sub = 'running', string $file = 'enabled'): string
    {
        return "Id=$id\nLoadState=$load\nActiveState=$active\nSubState=$sub\nUnitFileState=$file";
    }

    private function probe(?\Closure $reader = null): ServiceObservation
    {
        return new class($reader) implements ServiceObservation {
            public int $calls = 0;
            public function __construct(private ?\Closure $reader) {}
            public function read(): ServiceObservationSnapshot
            {
                ++$this->calls;
                return $this->reader === null ? ServiceObservationSnapshot::fromSystemdShow("Id=nginx.service\nLoadState=loaded\nActiveState=active\nSubState=running\nUnitFileState=enabled\n") : ($this->reader)();
            }
        };
    }

    private function overview(ServiceObservation $probe): ServiceOverview
    {
        return new ServiceOverview(new CandidateAuthorization($this->store->db), $probe);
    }

    private function state(): array
    {
        $state = [];
        $tables = $this->store->db->createSchemaManager()->listTableNames();
        sort($tables);
        foreach ($tables as $table) {
            if (!str_starts_with($table, 'wcp_candidate_')) continue;
            $rows = $this->store->db->fetchAllAssociative('SELECT * FROM '.$this->store->db->quoteIdentifier($table));
            usort($rows, static fn (array $a, array $b): int => strcmp(json_encode($a, JSON_THROW_ON_ERROR), json_encode($b, JSON_THROW_ON_ERROR)));
            $state[$table] = $rows;
        }
        return $state;
    }

    private static function assertReportBoundary(array $report, string $status): void
    {
        self::assertSame(['schema','observed_at','scope','status','coverage_complete','control_available','health_verified','services'], array_keys($report));
        self::assertSame('wcp360.candidate.service-overview/1', $report['schema']);
        self::assertMatchesRegularExpression('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z\z/D', $report['observed_at']);
        self::assertSame('current_process_namespace', $report['scope']);
        self::assertSame($status, $report['status']);
        foreach (['coverage_complete','control_available','health_verified'] as $flag) self::assertFalse($report[$flag]);
        self::assertSame(['nginx','apache','php_fpm','caddy','frankenphp','postgresql','mysql','redis','docker'], array_column($report['services'], 'id'));
        foreach ($report['services'] as $service) self::assertSame(['id','label','owner','observation','units'], array_keys($service));
    }

    public function testCurrentRootAdministratorReceivesPartialFactsWithoutBusinessWritesOrHealthClaims(): void
    {
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users']['admin']]);
        $raw = implode("\n\n", [
            self::block('php8.4-fpm.service'), self::block('postgresql.service', 'loaded', 'active', 'exited', 'static'),
            self::block('nginx.service', 'not-found', 'inactive', 'dead', ''),
            self::block('caddy.service', 'future-load', 'UNKNOWN_NEXT', 'future_mode-9', ''),
            self::block('php8.1-fpm.service', 'loaded', 'inactive', 'dead', 'disabled'),
            self::block('mariadb.service'), self::block('mariadb.service'),
        ]);
        $probe = $this->probe(static fn (): ServiceObservationSnapshot => ServiceObservationSnapshot::fromSystemdShow($raw));
        $before = $this->state();
        $report = $this->overview($probe)->inspect($this->fixture['users']['admin']);
        self::assertReportBoundary($report, 'observed');
        self::assertSame(1, $probe->calls);
        $services = array_column($report['services'], null, 'id');
        self::assertSame('observed', $services['nginx']['observation']);
        self::assertSame('not-found', $services['nginx']['units'][0]['load_state']);
        self::assertNull($services['nginx']['units'][0]['unit_file_state']);
        self::assertSame(['php8.1-fpm.service','php8.4-fpm.service'], array_column($services['php_fpm']['units'], 'id'));
        self::assertSame('exited', $services['postgresql']['units'][0]['sub_state']);
        self::assertSame('UNKNOWN_NEXT', $services['caddy']['units'][0]['active_state']);
        self::assertSame(['mariadb.service'], array_column($services['mysql']['units'], 'id'));
        foreach (['apache','frankenphp','redis','docker'] as $id) {
            self::assertSame('not_observed', $services[$id]['observation']);
            self::assertSame([], $services[$id]['units']);
        }
        foreach ($report['services'] as $service) {
            foreach ($service['units'] as $unit) self::assertSame(['id','load_state','active_state','sub_state','unit_file_state'], array_keys($unit));
        }
        self::assertSame($before, $this->state());
    }

    public function testUsernameRootAndAnonymousOrTenantActorsNeverStartCollection(): void
    {
        $id = $this->fixture['users']['alice'];
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $id]);
        $principal = new Principal($id, 'root', 'synthetic@example.test', '', 'user');
        self::assertSame(['ROLE_USER'], $principal->getRoles());
        $probe = $this->probe();
        $overview = $this->overview($probe);
        $before = $this->state();
        foreach ([$id, $this->fixture['users']['bob'], WorkbenchStore::id(), 'invalid-id', ''] as $actor) {
            try { $overview->inspect($actor); self::fail('Service authority inferred from identity.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
        self::assertSame(0, $probe->calls);
        self::assertSame($before, $this->state());
    }

    public function testInactiveOrReassignedAdministratorIsRefusedBeforeAnyNewCollection(): void
    {
        $probe = $this->probe();
        $overview = $this->overview($probe);
        $actor = $this->fixture['users']['admin'];
        $overview->inspect($actor);
        foreach ([['active' => 0], ['active' => 1, 'context' => 'user']] as $change) {
            $this->store->db->update('wcp_candidate_users', $change, ['id' => $actor]);
            $before = $this->state();
            try { $overview->inspect($actor); self::fail('Stale service authority was admitted.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
            self::assertSame($before, $this->state());
        }
        self::assertSame(1, $probe->calls);
    }

    public static function revokedSources(): iterable
    {
        foreach ([false, true] as $throws) {
            yield 'inactive/'.($throws ? 'source failure' : 'snapshot') => [['active' => 0], $throws];
            yield 'new context/'.($throws ? 'source failure' : 'snapshot') => [['context' => 'user'], $throws];
        }
    }

    #[DataProvider('revokedSources')]
    public function testRevocationDuringSuccessfulOrFailedCollectionPreventsPublishing(array $change, bool $throws): void
    {
        $actor = $this->fixture['users']['admin'];
        $probe = $this->probe(function () use ($actor, $change, $throws): ServiceObservationSnapshot {
            $this->store->db->update('wcp_candidate_users', $change, ['id' => $actor]);
            if ($throws) throw new \RuntimeException('private-native-observation-cause');
            return ServiceObservationSnapshot::fromSystemdShow(self::block('nginx.service'));
        });
        try {
            $this->overview($probe)->inspect($actor);
            self::fail('Revoked administrator received a service report.');
        } catch (AccessDeniedHttpException $error) {
            self::assertSame(403, $error->getStatusCode());
            self::assertNull($error->getPrevious());
        }
        self::assertSame(1, $probe->calls);
    }

    public static function unavailableSources(): iterable
    {
        yield 'not configured' => ['not_configured', 'not_configured'];
        yield 'failed snapshot' => ['failed', 'failed'];
        yield 'source exception' => ['exception', 'failed'];
        yield 'source error' => ['error', 'failed'];
    }

    #[DataProvider('unavailableSources')]
    public function testUnavailableSourcesNeverPublishPrivateErrorsStaleUnitsOrAbsenceClaims(string $source, string $status): void
    {
        $probe = $this->probe(static fn (): ServiceObservationSnapshot => match ($source) {
            'not_configured' => ServiceObservationSnapshot::notConfigured(),
            'failed' => ServiceObservationSnapshot::failed(),
            'exception' => throw new \RuntimeException('private-source-output /etc/private/native.conf'),
            default => throw new \Error('private-source-error'),
        });
        $before = $this->state();
        $report = $this->overview($probe)->inspect($this->fixture['users']['admin']);
        self::assertReportBoundary($report, $status);
        self::assertSame(1, $probe->calls);
        foreach ($report['services'] as $service) {
            self::assertSame('unavailable', $service['observation']);
            self::assertSame([], $service['units']);
        }
        self::assertStringNotContainsString('private', json_encode($report, JSON_THROW_ON_ERROR));
        self::assertSame($before, $this->state());
    }

    public function testAReportCanNeverCacheThePreviousObservationAcrossSourceFailure(): void
    {
        $reads = 0;
        $probe = $this->probe(static function () use (&$reads): ServiceObservationSnapshot {
            if (++$reads === 1) return ServiceObservationSnapshot::fromSystemdShow(self::block('docker.service'));
            throw new \RuntimeException('private-later-failure');
        });
        $overview = $this->overview($probe);
        $before = $this->state();
        $first = $overview->inspect($this->fixture['users']['admin']);
        $second = $overview->inspect($this->fixture['users']['admin']);
        self::assertSame('observed', $first['status']);
        self::assertSame('observed', $first['services'][8]['observation']);
        self::assertReportBoundary($second, 'failed');
        foreach ($second['services'] as $service) {
            self::assertSame('unavailable', $service['observation']);
            self::assertSame([], $service['units']);
        }
        self::assertSame(2, $probe->calls);
        self::assertSame($before, $this->state());
    }
}
