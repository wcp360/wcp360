<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Psr\Log\AbstractLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\IdentityAccess\Application\ListHostingUsers;
use Wcp360\Core\Module\IdentityAccess\Domain\UserListQuery;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingUsersFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ListHostingUsersTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;
    private array $ids;
    private ListHostingUsers $users;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->ids = HostingUsersFixture::seed($this->store->db, $this->fixture);
        $this->users = self::getContainer()->get(ListHostingUsers::class);
    }

    private function page(array $input = []): array
    {
        return $this->users->page($this->fixture['users']['admin'], UserListQuery::fromArray($input));
    }

    public function testTwoWayTraversalIncludesMembershipFreeUsersAndHasNoGapsOrDuplicates(): void
    {
        $first = $this->page(['q' => 'acct']);
        self::assertNull($first['pagination']['previous']);
        $second = $this->page(['q' => 'acct', 'after' => $first['pagination']['next']]);
        self::assertSame($first, $this->page(['q' => 'acct', 'before' => $second['pagination']['previous']]));
        // A newly matching row before the cursor must not shift the next page.
        $this->store->db->update('wcp_candidate_users', ['username' => 'acct0000'], ['id' => $this->fixture['users']['alice']]);
        self::assertSame($second, $this->page(['q' => 'acct', 'after' => $first['pagination']['next']]));
        $seen = array_column($first['items'], 'id');
        $page = $first;
        while ($page['pagination']['next'] !== null) {
            $page = $this->page(['q' => 'acct', 'after' => $page['pagination']['next']]);
            $seen = [...$seen, ...array_column($page['items'], 'id')];
        }
        self::assertSame($this->ids, $seen);
        self::assertCount(120, array_unique($seen));
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_memberships WHERE user_id=?", [$this->ids[0]]));
    }

    public function testActiveFiltersAndLiteralUnderscoresNeverBecomeWildcardSearches(): void
    {
        self::assertCount(60, $this->page(['q' => 'acct', 'state' => 'active', 'limit' => '100'])['items']);
        $inactive = $this->page(['q' => 'acct', 'state' => 'inactive', 'limit' => '100'])['items'];
        self::assertCount(60, $inactive);
        foreach ($inactive as $row) self::assertFalse($row['active']);
        $this->store->db->update('wcp_candidate_users', ['username' => 'acct_01'], ['id' => $this->ids[0]]);
        $this->store->db->update('wcp_candidate_users', ['username' => 'acctx01'], ['id' => $this->ids[1]]);
        self::assertSame([$this->ids[0]], array_column($this->page(['q' => 'ACCT_'])['items'], 'id'));
    }

    public function testProjectionDoesNotExposeCredentialsRootAccountsOrJoinMembershipsAndDoesNotWrite(): void
    {
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $page = $this->page(['limit' => '100']);
        self::assertCount(100, $page['items']);
        self::assertSame(['id','username','email','active'], array_keys($page['items'][0]));
        self::assertNotContains($this->fixture['users']['admin'], array_column($page['items'], 'id'));
        self::assertSame([], $this->page(['q' => 'admin'])['items']);
        $json = json_encode($page, JSON_THROW_ON_ERROR);
        foreach (['password_hash','password_revision','lock_guard','context','mfa','session',WorkbenchFixture::PASSWORD] as $private) self::assertStringNotContainsString($private, $json);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testMissingAdminAndFilterExcludedPivotsAreNotValidCursors(): void
    {
        foreach ([WorkbenchStore::id(), $this->fixture['users']['admin'], $this->fixture['users']['alice']] as $id) {
            try { $this->page(['q' => 'acct', 'after' => $id]); self::fail('Excluded pivot admitted.'); }
            catch (BadRequestHttpException $error) { self::assertSame(400, $error->getStatusCode()); }
        }
        self::assertSame([], $this->page(['q' => 'unknown_account'])['items']);
    }

    public function testUserAndRevokedRootAuthorityCannotListAccounts(): void
    {
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id(), 'invalid-id'] as $id) {
            try { $this->users->page($id, UserListQuery::fromArray([])); self::fail('User obtained global account visibility.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->expectException(AccessDeniedHttpException::class);
        $this->page();
    }

    public function testRevocationBetweenAdmissionAndPageReadIsNotAnAuthorizedEmptyPage(): void
    {
        if ($this->store->db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) self::markTestSkipped('Real PostgreSQL race connection is exercised in remote CI.');
        $owner = $this->store->db;
        $admin = $this->fixture['users']['admin'];
        $logger = new class($owner, $admin) extends AbstractLogger {
            private bool $fired = false;
            public function __construct(private \Doctrine\DBAL\Connection $owner, private string $actor) {}
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                if (!$this->fired && str_contains((string) ($context['sql'] ?? ''), 'SELECT u.id,u.username,u.email,u.active')) {
                    $this->fired = true;
                    $this->owner->update('wcp_candidate_users', ['active' => 0], ['id' => $this->actor]);
                }
            }
        };
        $config = new Configuration();
        $config->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware($logger)]);
        $db = DriverManager::getConnection($owner->getParams(), $config);
        try {
            $service = new ListHostingUsers($db, new CandidateAuthorization($db));
            $this->expectException(AccessDeniedHttpException::class);
            $service->page($admin, UserListQuery::fromArray([]));
        } finally { $db->close(); }
    }

    public function testSqliteRetainsAsciiUsernameOrderingAndExactUnderscoreSearch(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $fixture = WorkbenchFixture::reset($db);
            $ids = HostingUsersFixture::seed($db, $fixture, 60);
            $db->update('wcp_candidate_users', ['username' => 'acct_01'], ['id' => $ids[0]]);
            $db->update('wcp_candidate_users', ['username' => 'acctx01'], ['id' => $ids[1]]);
            $users = new ListHostingUsers($db, new CandidateAuthorization($db));
            $query = UserListQuery::fromArray(['q' => 'acct_']);
            self::assertSame([$ids[0]], array_column($users->page($fixture['users']['admin'], $query)['items'], 'id'));
            $page = $users->page($fixture['users']['admin'], UserListQuery::fromArray(['q' => 'acct', 'limit' => '100']));
            $names = array_column($page['items'], 'username');
            $sorted = $names; sort($sorted, SORT_STRING);
            self::assertSame($sorted, $names);
        } finally { $db->close(); }
    }

    public function testSelectedPermissionListingExcludesOtherUsersAndAllowsAnEmptySelectedAccount(): void
    {
        $service = self::getContainer()->get(CandidatePermissionAdministration::class);
        $selected = $service->listing($this->fixture['users']['admin'], 1, $this->fixture['users']['alice']);
        self::assertSame(['id' => $this->fixture['users']['alice'], 'username' => 'alice'], $selected['selected_user']);
        self::assertCount(1, $selected['items']);
        self::assertSame($this->fixture['users']['alice'], $selected['items'][0]['user_id']);
        self::assertSame([], $service->listing($this->fixture['users']['admin'], 1, $this->ids[0])['items']);
        foreach ([WorkbenchStore::id(), $this->fixture['users']['admin']] as $target) {
            try { $service->listing($this->fixture['users']['admin'], 1, $target); self::fail('Invalid selected identity admitted.'); }
            catch (NotFoundHttpException $error) { self::assertSame(404, $error->getStatusCode()); }
        }
    }
}
