<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Psr\Log\AbstractLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Observability\Application\ListAuditEvents;
use Wcp360\Core\Module\Observability\Domain\AuditListQuery;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\AuditEventsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ListAuditEventsTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private ListAuditEvents $events;
    private array $fixture;
    private array $ids;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->ids = AuditEventsFixture::seed($this->store->db, $this->fixture);
        $this->events = self::getContainer()->get(ListAuditEvents::class);
    }

    private function page(array $input = []): array
    {
        return $this->events->page($this->fixture['users']['admin'], AuditListQuery::fromArray($input));
    }

    public function testBidirectionalTraversalKeepsTimestampTiesAndHasNoDuplicateOrMissingEvents(): void
    {
        $first = $this->page();
        self::assertNull($first['pagination']['previous']);
        $seen = array_column($first['items'], 'id');
        $second = $this->page(['after' => $first['pagination']['next']]);
        self::assertSame($seen, array_column($this->page(['before' => $second['pagination']['previous']])['items'], 'id'));
        $current = $first;
        while ($current['pagination']['next'] !== null) {
            $current = $this->page(['after' => $current['pagination']['next']]);
            $seen = [...$seen, ...array_column($current['items'], 'id')];
            self::assertLessThanOrEqual(25, count($current['items']));
        }
        self::assertSame(array_reverse($this->ids), $seen);
        self::assertCount(120, array_unique($seen));
        $this->store->db->insert('wcp_candidate_audit', ['id' => AuditEventsFixture::id(9999), 'actor_id' => null,
            'tenant_id' => null, 'kind' => 'candidate.synthetic.new', 'resource_id' => null, 'created_at' => '2026-11-03T00:00:00Z', 'request_id' => null]);
        self::assertSame(array_column($second['items'], 'id'), array_column($this->page(['after' => $first['pagination']['next']])['items'], 'id'));
    }

    public function testAllExactFiltersComposeAndUtcBoundsAreInclusive(): void
    {
        $filters = ['kind' => 'hosting.plan.succeeded', 'actor' => $this->fixture['users']['bob'],
            'tenant' => $this->fixture['tenants']['bob'], 'request' => str_repeat('b', 32),
            'from' => '2026-11-02T00:00:00Z', 'until' => '2026-11-02T00:00:00Z', 'limit' => '100'];
        self::assertCount(30, $this->page($filters)['items']);
        $items = $this->page($filters + ['resource' => AuditEventsFixture::id(10086)])['items'];
        self::assertSame([AuditEventsFixture::id(86)], array_column($items, 'id'));
        self::assertSame([], $this->page(['request' => str_repeat('f', 32)])['items']);
        self::assertSame([], $this->page(['actor' => $this->fixture['users']['admin']])['items']);
    }

    public function testMissingAndFilterExcludedCursorsAreRefused(): void
    {
        foreach ([['after' => AuditEventsFixture::id(9999)],
            ['kind' => 'hosting.plan.succeeded', 'after' => AuditEventsFixture::id(1)]] as $input) {
            try { $this->page($input); self::fail('Unavailable cursor admitted.'); }
            catch (BadRequestHttpException $error) { self::assertSame(400, $error->getStatusCode()); }
        }
    }

    public function testUserMembershipAndRandomIdentityNeverGrantGlobalAuditReadAuthority(): void
    {
        foreach ([$this->fixture['users']['alice'], $this->fixture['users']['bob'], WorkbenchStore::id(), 'invalid-id'] as $actor) {
            try { $this->events->page($actor, AuditListQuery::fromArray([])); self::fail('Audit authority inferred.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
    }

    public function testCurrentAdminInactivationAndContextChangeRevokeExistingCursors(): void
    {
        $cursor = $this->page()['pagination']['next'];
        foreach ([['active' => 0], ['active' => 1, 'context' => 'user']] as $change) {
            $this->store->db->update('wcp_candidate_users', $change, ['id' => $this->fixture['users']['admin']]);
            try { $this->page(['after' => $cursor]); self::fail('Stale admin authority used.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
    }

    public function testProjectionIncludesNullableSystemEventsWithoutJoiningPrivateAccountDataOrWritingAudit(): void
    {
        $this->store->db->insert('wcp_candidate_audit', ['id' => AuditEventsFixture::id(9999), 'actor_id' => null,
            'tenant_id' => null, 'kind' => 'candidate.synthetic.system', 'resource_id' => null, 'created_at' => '2026-11-03T00:00:00Z', 'request_id' => null]);
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $page = $this->page(['limit' => '100']);
        self::assertCount(100, $page['items']);
        self::assertSame(['id','actor_id','tenant_id','kind','resource_id','created_at','request_id'], array_keys($page['items'][0]));
        self::assertNull($page['items'][0]['actor_id']);
        self::assertNull($page['items'][0]['tenant_id']);
        self::assertNull($page['items'][0]['resource_id']);
        self::assertNull($page['items'][0]['request_id']);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
        $json = json_encode($page, JSON_THROW_ON_ERROR);
        foreach (['password_hash','email','username','idempotency_key','fingerprint',WorkbenchFixture::PASSWORD] as $private) self::assertStringNotContainsString($private, $json);
    }

    public function testSameTieAndCursorBehaviorOnSqlite(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $fixture = WorkbenchFixture::reset($db);
            $ids = AuditEventsFixture::seed($db, $fixture, 60);
            $events = new ListAuditEvents($db, new CandidateAuthorization($db));
            $first = $events->page($fixture['users']['admin'], AuditListQuery::fromArray([]));
            $next = $events->page($fixture['users']['admin'], AuditListQuery::fromArray(['after' => $first['pagination']['next']]));
            self::assertSame(array_slice(array_reverse($ids), 0, 25), array_column($first['items'], 'id'));
            self::assertSame(array_slice(array_reverse($ids), 25, 25), array_column($next['items'], 'id'));
            self::assertSame($first, $events->page($fixture['users']['admin'], AuditListQuery::fromArray(['before' => $next['pagination']['previous']])));
        } finally { $db->close(); }
    }

    public function testRevocationBetweenAdmissionAndPageReadFailsClosed(): void
    {
        if ($this->store->db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) self::markTestSkipped('Separate real PostgreSQL connection is exercised in remote CI.');
        $owner = $this->store->db;
        $admin = $this->fixture['users']['admin'];
        $logger = new class($owner, $admin) extends AbstractLogger {
            private bool $fired = false;
            public function __construct(private \Doctrine\DBAL\Connection $owner, private string $actor) {}
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                if (!$this->fired && str_contains((string) ($context['sql'] ?? ''), 'SELECT a.id,a.actor_id')) {
                    $this->fired = true;
                    $this->owner->update('wcp_candidate_users', ['active' => 0], ['id' => $this->actor]);
                }
            }
        };
        $configuration = new Configuration();
        $configuration->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware($logger)]);
        $db = DriverManager::getConnection($owner->getParams(), $configuration);
        try {
            $events = new ListAuditEvents($db, new CandidateAuthorization($db));
            $this->expectException(AccessDeniedHttpException::class);
            $events->page($admin, AuditListQuery::fromArray([]));
        } finally { $db->close(); }
    }
}
