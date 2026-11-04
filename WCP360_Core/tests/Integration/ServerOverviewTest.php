<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ServerObservation;
use Wcp360\Core\Module\Platform\Application\ServerOverview;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ServerOverviewTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    public static function sample(): array
    {
        return ['os_family' => 'Linux', 'platform' => 'debian-13', 'architecture' => 'x86_64',
            'memory' => ['total_bytes' => 1073741824, 'available_bytes' => 0],
            'disk' => ['path' => '/var', 'total_bytes' => 2147483648, 'free_bytes' => 0]];
    }

    private function probe(?\Closure $read = null): ServerObservation
    {
        return new class($read) implements ServerObservation {
            public int $calls = 0;
            public function __construct(private ?\Closure $reader) {}
            public function read(): array { ++$this->calls; return $this->reader === null ? ServerOverviewTest::sample() : ($this->reader)(); }
        };
    }

    public function testCurrentRootAdministratorGetsExactScopedObservationsWithoutBusinessWrites(): void
    {
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users']['admin']]);
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $probe = $this->probe();
        $overview = new ServerOverview(new CandidateAuthorization($this->store->db), $probe);
        $report = $overview->inspect($this->fixture['users']['admin']);
        self::assertSame('wcp360.candidate.server-overview/1', $report['schema']);
        self::assertSame('current_process_namespace', $report['scope']);
        self::assertSame(self::sample(), $report['server']);
        self::assertMatchesRegularExpression('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z\z/D', $report['observed_at']);
        self::assertSame(1, $probe->calls);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testUsernameRootNeverGrantsUserContextServerAuthorityOrStartsCollection(): void
    {
        $id = $this->fixture['users']['alice'];
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $id]);
        $principal = new Principal($id, 'root', 'synthetic@example.test', '', 'user');
        self::assertSame(['ROLE_USER'], $principal->getRoles());
        $probe = $this->probe();
        $overview = new ServerOverview(new CandidateAuthorization($this->store->db), $probe);
        foreach ([$id, $this->fixture['users']['bob'], WorkbenchStore::id(), 'invalid-id'] as $actor) {
            try { $overview->inspect($actor); self::fail('Server authority inferred from identity.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
        self::assertSame(0, $probe->calls);
    }

    public function testInactivationAndContextChangeRejectAnExistingAdministratorBeforeCollection(): void
    {
        $probe = $this->probe();
        $overview = new ServerOverview(new CandidateAuthorization($this->store->db), $probe);
        $overview->inspect($this->fixture['users']['admin']);
        foreach ([['active' => 0], ['active' => 1, 'context' => 'user']] as $change) {
            $this->store->db->update('wcp_candidate_users', $change, ['id' => $this->fixture['users']['admin']]);
            try { $overview->inspect($this->fixture['users']['admin']); self::fail('Stale root authority admitted.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
        self::assertSame(1, $probe->calls);
    }

    public function testRevocationDuringCollectionPreventsPublishingTheObservation(): void
    {
        $probe = $this->probe(function (): array {
            $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
            return self::sample();
        });
        $overview = new ServerOverview(new CandidateAuthorization($this->store->db), $probe);
        $this->expectException(AccessDeniedHttpException::class);
        $overview->inspect($this->fixture['users']['admin']);
    }

    public function testLiveLocalAdapterProjectsOnlyCapacityWithoutNetworkProcessOrPrivatePaths(): void
    {
        $report = self::getContainer()->get(ServerOverview::class)->inspect($this->fixture['users']['admin']);
        self::assertSame(['os_family','platform','architecture','memory','disk','runtime'], array_keys($report['server']));
        self::assertSame(['uptime_seconds','load_average'], array_keys($report['server']['runtime']));
        self::assertSame(PHP_OS_FAMILY, $report['server']['os_family']);
        self::assertLessThanOrEqual(64, strlen($report['server']['architecture']));
        if ($report['server']['memory'] !== null) {
            self::assertGreaterThan(0, $report['server']['memory']['total_bytes']);
            self::assertGreaterThanOrEqual(0, $report['server']['memory']['available_bytes']);
            self::assertLessThanOrEqual($report['server']['memory']['total_bytes'], $report['server']['memory']['available_bytes']);
        }
        foreach (['network','managed_paths','effective_uid','systemd_marker','password','database_url','hostname'] as $private) self::assertArrayNotHasKey($private, $report['server']);
    }

    public function testWindowsUnknownCapacityRemainsNullAndSqliteAuthorizationStillApplies(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $fixture = WorkbenchFixture::reset($db);
            $probe = $this->probe(static fn (): array => ['os_family' => 'Windows', 'platform' => null,
                'architecture' => 'AMD64', 'memory' => null, 'disk' => null]);
            $overview = new ServerOverview(new CandidateAuthorization($db), $probe);
            self::assertNull($overview->inspect($fixture['users']['admin'])['server']['memory']);
            self::assertNull($overview->inspect($fixture['users']['admin'])['server']['disk']);
            $this->expectException(AccessDeniedHttpException::class);
            $overview->inspect($fixture['users']['alice']);
        } finally { $db->close(); }
    }
    public function testOptionalRuntimeMeasurementsPreserveZeroAndNullWithoutWrites(): void
    {
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        foreach ([
            ['uptime_seconds' => 0, 'load_average' => ['one_minute' => 0.0, 'five_minutes' => 0.0, 'fifteen_minutes' => 0.0]],
            ['uptime_seconds' => null, 'load_average' => ['one_minute' => 1.0, 'five_minutes' => 2.0, 'fifteen_minutes' => 3.0]],
            ['uptime_seconds' => 86400, 'load_average' => null],
        ] as $runtime) {
            $probe = $this->probe(static fn (): array => self::sample() + ['runtime' => $runtime]);
            $overview = new ServerOverview(new CandidateAuthorization($this->store->db), $probe);
            self::assertSame($runtime, $overview->inspect($this->fixture['users']['admin'])['server']['runtime']);
            self::assertSame(1, $probe->calls);
        }
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }
}
