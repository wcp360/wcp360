<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use PHPUnit\Framework\Attributes\DataProvider;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class SessionJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    /** @var array{users: array<string, string>, tenants: array<string, string>} */
    private array $fixture;
    private string $surface = 'user';

    protected function setUp(): void
    {
        $this->bootBrowser('user', true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->context('user');
    }

    private function context(string $surface): void
    {
        $this->surface = $surface;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $surface;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $surface.'.localhost';
    }

    private function bootBrowser(string $surface, bool $reset = false): void
    {
        self::ensureKernelShutdown();
        $this->context($surface);
        $this->browser = self::createClient([], [
            'HTTP_HOST' => $surface.'.localhost',
            'HTTP_ORIGIN' => 'http://'.$surface.'.localhost',
        ]);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        if ($reset) $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function url(string $path): string
    {
        return 'http://'.$this->surface.'.localhost'.$path;
    }

    private function login(string $name = 'alice'): Crawler
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form([
            '_username' => $name, '_password' => WorkbenchFixture::PASSWORD,
        ]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); $i++) {
            $crawler = $this->browser->followRedirect();
        }
        self::assertResponseIsSuccessful();
        $enabled = $this->surface === 'admin' && is_string($this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->fixture['users']['admin']]));
        self::assertSame($enabled ? '/mfa' : '/', $this->browser->getRequest()->getPathInfo());

        return $crawler;
    }

    /** @return list<Cookie> */
    private function cookies(): array
    {
        $session = $this->browser->getRequest()->getSession();
        if ($session->isStarted()) $session->save();

        return $this->browser->getCookieJar()->all();
    }

    /** @param list<Cookie> $cookies */
    private function useCookies(array $cookies): void
    {
        $this->browser->getCookieJar()->clear();
        foreach ($cookies as $cookie) $this->browser->getCookieJar()->set($cookie);
    }

    private function newBrowserLogin(string $name = 'alice'): Crawler
    {
        $this->useCookies([]);

        return $this->login($name);
    }

    private function selector(): string
    {
        $registry = $this->browser->getRequest()->getSession()->get('_wcp_registry');
        self::assertIsArray($registry);
        self::assertSame($this->surface, $registry['context'] ?? null);
        self::assertIsString($registry['actor'] ?? null);
        self::assertIsString($registry['selector'] ?? null);

        return $registry['selector'];
    }

    /** @return array<string, string> */
    private function fields(string $selector): array
    {
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        $form = $crawler->filter('form[action="/account/sessions/'.$selector.'/revoke"]')->form();
        $values = $form->getValues();
        self::assertIsString($values['_token'] ?? null);

        return ['_token' => $values['_token'], 'current_password' => WorkbenchFixture::PASSWORD];
    }

    /** A real target-specific token deliberately isolates ownership checks from CSRF checks. */
    private function targetedCsrf(string $selector): string
    {
        $requests = self::getContainer()->get(RequestStack::class);
        $requests->push($this->browser->getRequest());
        try {
            $token = self::getContainer()->get(CsrfTokenManagerInterface::class)->getToken('session_revoke_'.$selector)->getValue();
            $this->browser->getRequest()->getSession()->save();

            return $token;
        } finally {
            $requests->pop();
        }
    }

    public function testFutureBrowserSessionHeartbeatIsRejectedInBothUserAndAdminContexts(): void
    {
        foreach (['user' => 'alice', 'admin' => 'admin'] as $surface => $username) {
            $this->bootBrowser($surface, true);
            $this->login($username);

            $session = $this->browser->getRequest()->getSession();
            $created = $session->get('_wcp_created');
            $last = $session->get('_wcp_last');
            self::assertIsInt($created);
            self::assertIsInt($last);
            self::assertLessThanOrEqual(time(), $last);

            // Keep the registry row valid while corrupting only the browser
            // activity clock. The safe result is logout, not extended access.
            $session->set('_wcp_last', time() + 3600);
            $session->save();

            $this->browser->request('GET', $this->url('/account/sessions'));
            self::assertResponseRedirects('/login');
            self::assertFalse($this->browser->getRequest()->getSession()->has('_wcp_registry'));
        }
    }

    public function testRevokeOthersPreservesCurrentCookieAndBlocksTheOtherCookieAfterRestart(): void
    {
        $this->login();
        $other = $this->selector();
        $otherCookies = $this->cookies();
        $this->newBrowserLogin();
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertSelectorExists('input#password-others[autocomplete="current-password"]');
        $this->browser->submit($crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/account/sessions', 303);
        $this->browser->followRedirect();
        self::assertSelectorTextContains('[role="status"]', 'Cette session reste ouverte.');
        self::assertSame($current, $this->selector());
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id=?', [$other]));
        $survivorCookies = $this->cookies();
        $this->bootBrowser('user');
        $this->useCookies($otherCookies);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseRedirects('/login');
        $this->useCookies($survivorCookies);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        self::assertSame($current, $this->selector());
    }

    public function testRevokeOthersWithNoOtherSessionReturnsAnExplicitNoOp(): void
    {
        $this->login();
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $this->browser->submit($crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/account/sessions', 303);
        $this->browser->followRedirect();
        self::assertSelectorTextContains('[role="status"]', 'Aucune autre session');
        self::assertSame($current, $this->selector());
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.session.others_revoked'"));
    }

    public function testRevokeOthersRequiresItsOwnCsrfOriginBoundBodyAndPasswordBudget(): void
    {
        $this->login();
        $this->newBrowserLogin();
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $token = $crawler->selectButton('Déconnecter les autres sessions')->form()->getValues()['_token'];
        $base = ['_token' => $token, 'current_password' => WorkbenchFixture::PASSWORD];
        foreach ([
            [$base + ['actor' => $this->fixture['users']['bob']], '', 'http://user.localhost', 400],
            [$base + ['selector' => $current], '', 'http://user.localhost', 400],
            [$base, '?return=https://example.test', 'http://user.localhost', 400],
            [array_replace($base, ['_token' => 'invalid']), '', 'http://user.localhost', 403],
            [$base, '', 'http://other.localhost', 403],
        ] as [$fields, $query, $origin, $status]) {
            $this->browser->request('POST', $this->url('/account/sessions/revoke-others'.$query), $fields, [], ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame($status);
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions WHERE id=?', [$current]));
        $singleToken = $this->targetedCsrf($current);
        $this->browser->request('POST', $this->url('/account/sessions/revoke-others'), array_replace($base, ['_token' => $singleToken]));
        self::assertResponseStatusCodeSame(403);
        $wrong = 'wrong-synthetic-password-never-echo';
        for ($i = 0; $i < 5; ++$i) {
            $this->browser->request('POST', $this->url('/account/sessions/revoke-others'),
                array_replace($base, ['current_password' => $wrong]), [], ['HTTP_ORIGIN' => 'http://user.localhost']);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString($wrong, (string) $this->browser->getResponse()->getContent());
        }
        $this->browser->request('POST', $this->url('/account/sessions/revoke-others'), $base);
        self::assertResponseStatusCodeSame(429);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL"));
    }

    public function testRevokeOthersHasNoGetEffectAndRequiresAuthentication(): void
    {
        $this->browser->request('POST', $this->url('/account/sessions/revoke-others'), ['current_password' => 'synthetic']);
        self::assertResponseRedirects('/login');
        $this->login();
        $this->browser->request('GET', $this->url('/account/sessions/revoke-others'));
        self::assertResponseStatusCodeSame(405);
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.session.others_revoked'"));
    }

    public function testPasswordOnlyRootCanRevokeOthersWithFreshPasswordProof(): void
    {
        $this->bootBrowser('admin', true);
        $this->login('admin');
        $other = $this->selector();
        $this->newBrowserLogin('admin');
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $this->browser->submit($crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/account/sessions', 303);
        $this->browser->followRedirect();
        self::assertSame($current, $this->selector());
        self::assertNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id=?', [$current]));
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id=?', [$other]));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa WHERE sealed_secret IS NOT NULL'));
    }

    public function testExpiredRootProofIsRefusedBeforeControllerWithoutExposingPassword(): void
    {
        $this->bootBrowser('admin', true);
        $this->login('admin');
        $this->newBrowserLogin('admin');
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $form = $crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]);
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->browser->submit($form);
        self::assertResponseStatusCodeSame(403);
        self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, (string) $this->browser->getResponse()->getContent());
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NOT NULL'));
    }

    public function testEnabledRootMfaAndPendingLoginRespectTheSameRevocationBoundary(): void
    {
        $pending = $this->pendingAdministrator();
        $this->browser->request('POST', $this->url('/account/sessions/revoke-others'), ['_token' => 'invalid', 'current_password' => WorkbenchFixture::PASSWORD]);
        self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', 'http://admin.localhost/mfa']);
        $this->browser->submit($pending['crawler']->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($pending['secret'])->now()]));
        self::assertResponseIsSuccessful();
        $current = $this->selector();
        $other = self::getContainer()->get(CandidateSessions::class)->issue($this->fixture['users']['admin'], 'admin', str_repeat('z', 32));
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery ORDER BY code_hash');
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $this->browser->submit($crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/account/sessions', 303);
        $this->browser->followRedirect();
        self::assertSame($current, $this->selector());
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id=?', [$other]));
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery ORDER BY code_hash'));
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT auth_strength FROM wcp_candidate_admin_sessions WHERE id=?', [$current]));
    }

    public function testRevokeOthersAuditFailureReturnsRedactedErrorAndRollsBack(): void
    {
        $this->login();
        $other = $this->selector();
        $this->newBrowserLogin();
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $form = $crawler->selectButton('Déconnecter les autres sessions')->form(['current_password' => WorkbenchFixture::PASSWORD]);
        $this->rejectAuditKind('candidate.session.others_revoked');
        try {
            $this->browser->submit($form);
            self::assertResponseStatusCodeSame(503);
            self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, (string) $this->browser->getResponse()->getContent());
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
            foreach ([$current, $other] as $selector) {
                self::assertNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id=?', [$selector]));
            }
        } finally { $this->allowAuditInserts(); }
    }

    public function testAccountListsOwnIndependentSessionsWithoutOtherAccounts(): void
    {
        $this->login();
        $first = $this->selector();
        $this->newBrowserLogin('bob');
        $foreign = $this->selector();
        $this->newBrowserLogin();
        $current = $this->selector();
        self::assertNotSame($first, $current);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mes sessions');
        self::assertSelectorTextContains('caption', 'Sessions de mon compte');
        self::assertSelectorCount(2, '[data-session-strength]');
        self::assertSelectorTextSame('[data-session-strength]', 'Mot de passe');
        self::assertSelectorExists('a[href="/account/sessions"]');
        self::assertSelectorCount(2, 'form[action$="/revoke"]');
        self::assertSelectorExists('form[action="/account/sessions/'.$first.'/revoke"]');
        self::assertSelectorExists('form[action="/account/sessions/'.$current.'/revoke"]');
        self::assertStringNotContainsString($foreign, (string) $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('bob@example.test', (string) $this->browser->getResponse()->getContent());
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
    }

    public function testRevokingAnotherSessionBlocksItsNextRequestAndPreservesCurrentSession(): void
    {
        $this->login();
        $target = $this->selector();
        $targetCookies = $this->cookies();
        $this->newBrowserLogin();
        $survivor = $this->selector();
        $fields = $this->fields($target);
        $survivorCookies = $this->cookies();
        $this->browser->request('POST', $this->url('/account/sessions/'.$target.'/revoke'), $fields);
        self::assertResponseRedirects('/account/sessions', 303);
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$target]));
        self::assertNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$survivor]));
        $this->useCookies($targetCookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        $this->useCookies($targetCookies);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseRedirects('/login');
        $this->useCookies($survivorCookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseIsSuccessful();
        self::assertSame($survivor, $this->selector());
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
    }

    public function testRevokeAllLogsOutEveryOwnSessionAndLeavesAnotherAccountConnected(): void
    {
        $this->login();
        $firstCookies = $this->cookies();
        $this->newBrowserLogin('bob');
        $bob = $this->selector();
        $bobCookies = $this->cookies();
        $this->newBrowserLogin();
        $secondCookies = $this->cookies();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $this->browser->submit($crawler->filter('form[action="/account/sessions/revoke-all"]')->form([
            'current_password' => WorkbenchFixture::PASSWORD,
        ]));
        self::assertResponseRedirects('/login', 303);
        foreach ([$firstCookies, $secondCookies] as $cookies) {
            $this->useCookies($cookies);
            $this->browser->request('GET', $this->url('/'));
            self::assertResponseRedirects('/login');
        }
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE user_id = ? AND revoked_at IS NOT NULL', [$this->fixture['users']['alice']]));
        self::assertSame(3, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
        $this->useCookies($bobCookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseIsSuccessful();
        self::assertSame($bob, $this->selector());
    }

    public function testRevocationRequiresExactOriginCsrfAndCurrentPasswordBeforeAnyChange(): void
    {
        $this->login();
        $target = $this->selector();
        $this->newBrowserLogin();
        $fields = $this->fields($target);
        $auditCount = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        $path = $this->url('/account/sessions/'.$target.'/revoke');
        foreach (['https://foreign.example', ''] as $origin) {
            $this->browser->request('POST', $path, $fields, [], ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame(403);
        }
        $this->browser->request('POST', $path, array_replace($fields, ['_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        self::assertSame($auditCount, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT SUM(reauth_attempts) FROM wcp_candidate_user_sessions'));
        foreach ([['current_password' => 'incorrect-password'], ['current_password' => '']] as $invalid) {
            $this->browser->request('POST', $path, array_replace($fields, $invalid));
            self::assertResponseStatusCodeSame(403);
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT SUM(reauth_attempts) FROM wcp_candidate_user_sessions'));
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind = 'candidate.session.revoked'"));
        $this->browser->request('POST', $path, $fields);
        self::assertResponseRedirects('/account/sessions', 303);
    }

    public function testValidCsrfCannotRevokeAnotherAccountOrAdministratorSurface(): void
    {
        $this->login('bob');
        $foreign = $this->selector();
        $this->newBrowserLogin();
        $this->browser->request('GET', $this->url('/account/sessions'));
        $admin = self::getContainer()->get(CandidateSessions::class)->issue($this->fixture['users']['admin'], 'admin', bin2hex(random_bytes(32)));
        foreach ([$foreign, $admin] as $target) {
            $this->browser->request('POST', $this->url('/account/sessions/'.$target.'/revoke'), [
                '_token' => $this->targetedCsrf($target), 'current_password' => WorkbenchFixture::PASSWORD,
            ]);
            self::assertResponseStatusCodeSame(403);
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NOT NULL'));
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind = 'candidate.session.revoked'"));
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        self::assertStringNotContainsString($admin, (string) $this->browser->getResponse()->getContent());
    }

    public function testRepeatedWrongPasswordsExhaustTheSessionQuotaBeforeAValidPasswordCanRevoke(): void
    {
        $this->login();
        $target = $this->selector();
        $this->newBrowserLogin();
        $fields = $this->fields($target);
        $path = $this->url('/account/sessions/'.$target.'/revoke');
        for ($i = 0; $i < 5; $i++) {
            $this->browser->request('POST', $path, array_replace($fields, ['current_password' => 'incorrect-password']));
            self::assertResponseStatusCodeSame(403);
        }
        $this->browser->request('POST', $path, $fields);
        self::assertResponseStatusCodeSame(429);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
        // A fresh, independently authenticated browser has its own bounded quota.
        $this->newBrowserLogin();
        $this->browser->request('POST', $path, $this->fields($target));
        self::assertResponseRedirects('/account/sessions', 303);
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$target]));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function auditFailures(): iterable
    {
        yield 'password confirmation audit' => ['candidate.session.reauthentication_attempted', false];
        yield 'selected session revocation audit' => ['candidate.session.revoked', false];
        yield 'all sessions revocation audit' => ['candidate.session.revoked', true];
    }

    #[DataProvider('auditFailures')]
    public function testAuditFailureReturnsAFixedResponseWithoutCommittingUnauditedChanges(string $failureKind, bool $all): void
    {
        $this->login();
        $target = $this->selector();
        $targetCookies = $this->cookies();
        $this->newBrowserLogin();
        $current = $this->selector();
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $path = $all ? '/account/sessions/revoke-all' : '/account/sessions/'.$target.'/revoke';
        $fields = $crawler->filter('form[action="'.$path.'"]')->form(['current_password' => WorkbenchFixture::PASSWORD])->getValues();
        $sid = $this->browser->getRequest()->getSession()->getId();
        $auditCount = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        $quotaFailure = $failureKind === 'candidate.session.reauthentication_attempted';
        $this->rejectAuditKind($failureKind);
        try {
            $this->browser->request('POST', $this->url($path), $fields);
            self::assertResponseStatusCodeSame(503);
            $body = (string) $this->browser->getResponse()->getContent();
            self::assertSame('Session management is unavailable. Try again later.', $body);
            foreach ([WorkbenchFixture::PASSWORD, $sid, 'session-audit-failure-internal-marker', 'wcp_candidate_', 'secret_verifier', 'SQLSTATE'] as $private) {
                self::assertStringNotContainsString($private, $body);
            }
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
            self::assertSame($quotaFailure ? 0 : 1, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions WHERE id = ?', [$current]));
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions WHERE id = ?', [$target]));
            self::assertSame($auditCount + ($quotaFailure ? 0 : 1), (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
            self::assertSame(0, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind = 'candidate.session.revoked'"));
        } finally {
            $this->allowAuditInserts();
        }
        // Both real sessions remain admitted after the failed transaction.
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        self::assertSame($current, $this->selector());
        $currentCookies = $this->cookies();
        $this->useCookies($targetCookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseIsSuccessful();
        self::assertSame($target, $this->selector());
        $this->useCookies($currentCookies);
        $this->browser->request('POST', $this->url($path), $fields);
        self::assertResponseRedirects($all ? '/login' : '/account/sessions', 303);
        self::assertSame($all ? 2 : 1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
    }

    private function rejectAuditKind(string $kind): void
    {
        if (!in_array($kind, ['candidate.session.reauthentication_attempted', 'candidate.session.revoked', 'candidate.session.others_revoked'], true)) {
            throw new \InvalidArgumentException('Unsupported audit failure injection.');
        }
        if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->store->db->executeStatement("CREATE TRIGGER wcp_test_session_http_audit_unavailable BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind = '$kind' BEGIN SELECT RAISE(ABORT, 'session-audit-failure-internal-marker'); END");
        } else {
            $this->store->db->executeStatement("CREATE OR REPLACE FUNCTION wcp_test_session_http_audit_unavailable() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN IF NEW.kind = ''$kind'' THEN RAISE EXCEPTION ''session-audit-failure-internal-marker''; END IF; RETURN NEW; END;'");
            $this->store->db->executeStatement('CREATE TRIGGER wcp_test_session_http_audit_unavailable BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_test_session_http_audit_unavailable()');
        }
    }

    private function allowAuditInserts(): void
    {
        if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->store->db->executeStatement('DROP TRIGGER wcp_test_session_http_audit_unavailable');
        } else {
            $this->store->db->executeStatement('DROP TRIGGER wcp_test_session_http_audit_unavailable ON wcp_candidate_audit');
            $this->store->db->executeStatement('DROP FUNCTION wcp_test_session_http_audit_unavailable()');
        }
    }

    public function testRevokingCurrentSessionLogsOutAndOldCookieCannotReturn(): void
    {
        $this->login();
        $current = $this->selector();
        $fields = $this->fields($current);
        $cookies = $this->cookies();
        $this->browser->request('POST', $this->url('/account/sessions/'.$current.'/revoke'), $fields);
        self::assertResponseRedirects('/login', 303);
        $this->useCookies($cookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$current]));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
    }

    public function testLogoutDurablyRevokesTheBoundSession(): void
    {
        $crawler = $this->login();
        $current = $this->selector();
        $cookies = $this->cookies();
        $this->browser->submit($crawler->filter('form[action="/logout"]')->form());
        self::assertResponseRedirects('/login');
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$current]));
        $this->useCookies($cookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
    }

    public function testRevocationAfterTheRequestGateStillBlocksAnUnsafeController(): void
    {
        $crawler = $this->login();
        $selector = $this->selector();
        $sessions = self::getContainer()->get(CandidateSessions::class);
        $actor = $this->fixture['users']['alice'];
        $form = $crawler->filter('form[action="/workbench/plans"]')->form(['hostname' => 'revoked-before-controller.test']);
        $dispatcher = self::getContainer()->get('event_dispatcher');
        self::assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
        $revoked = false;
        $revokeBeforeController = static function (ControllerEvent $event) use ($sessions, $actor, $selector, &$revoked): void {
            if ($event->isMainRequest() && $event->getRequest()->getPathInfo() === '/workbench/plans') {
                $revoked = $sessions->revoke($actor, 'user', $selector);
            }
        };
        $dispatcher->addListener('kernel.controller', $revokeBeforeController, 64);
        try {
            $this->browser->submit($form);
            self::assertTrue($revoked);
            self::assertResponseStatusCodeSame(403);
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
        } finally {
            $dispatcher->removeListener('kernel.controller', $revokeBeforeController);
        }
    }

    public function testBrowserSessionIdentifiersAreAbsentFromDatabaseAuditAndRenderedAccount(): void
    {
        $this->login();
        $sid = $this->browser->getRequest()->getSession()->getId();
        self::assertNotSame('', $sid);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        $rows = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_user_sessions');
        self::assertCount(1, $rows);
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/D', (string) $rows[0]['secret_verifier']);
        foreach ([
            (string) $this->browser->getResponse()->getContent(),
            json_encode($rows, JSON_THROW_ON_ERROR),
            json_encode($this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'), JSON_THROW_ON_ERROR),
        ] as $representation) {
            self::assertStringNotContainsString($sid, $representation);
            self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $representation);
        }
    }

    public function testLegacyCookieWithoutDurableRegistrationCannotRegainAccess(): void
    {
        $this->login();
        $session = $this->browser->getRequest()->getSession();
        $session->remove('_wcp_registry');
        $session->save();
        $cookies = $this->cookies();
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        $this->useCookies($cookies);
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseRedirects('/login');
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
    }

    public function testAnotherOwnSelectorCannotBeSubstitutedForTheCookieBinding(): void
    {
        $this->login();
        $first = $this->selector();
        $firstCookies = $this->cookies();
        $this->newBrowserLogin();
        $session = $this->browser->getRequest()->getSession();
        $registry = $session->get('_wcp_registry');
        self::assertIsArray($registry);
        $registry['selector'] = $first;
        $session->set('_wcp_registry', $registry);
        $session->save();
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        $this->useCookies($firstCookies);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseIsSuccessful();
        self::assertSame($first, $this->selector());
    }

    /** @return array{crawler:Crawler,secret:string,selector:string,sid:string} */
    private function pendingAdministrator(): array
    {
        $this->bootBrowser('admin', true);
        $this->login('admin');
        $crawler = $this->browser->request('GET', $this->url('/account/security/mfa/enable'));
        $crawler = $this->browser->submit($crawler->selectButton('Configurer la MFA')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseIsSuccessful();
        $selector = $this->selector();
        $sid = $this->browser->getRequest()->getSession()->getId();
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT auth_strength FROM wcp_candidate_admin_sessions WHERE id = ?', [$selector]));

        return ['crawler' => $crawler, 'secret' => trim($crawler->filter('#mfa-secret')->text()), 'selector' => $selector, 'sid' => $sid];
    }

    public function testSuccessfulMfaRotatesBindingAndUpgradesTheSameDurableSession(): void
    {
        $pending = $this->pendingAdministrator();
        $oldVerifier = $this->store->db->fetchOne('SELECT secret_verifier FROM wcp_candidate_admin_sessions WHERE id = ?', [$pending['selector']]);
        $this->browser->submit($pending['crawler']->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($pending['secret'])->now()]));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, '#recovery-codes code');
        self::assertSame($pending['selector'], $this->selector());
        self::assertNotSame($pending['sid'], $this->browser->getRequest()->getSession()->getId());
        $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_admin_sessions WHERE id = ?', [$pending['selector']]);
        self::assertIsArray($row);
        self::assertSame(2, (int) $row['auth_strength']);
        self::assertNotSame($oldVerifier, $row['secret_verifier']);
        self::assertNull($row['revoked_at']);
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[action="/account/sessions/'.$pending['selector'].'/revoke"]');
        self::assertSelectorTextSame('tr[data-session-id="'.$pending['selector'].'"] [data-session-strength]', 'Mot de passe + MFA');
        self::assertStringNotContainsString($pending['sid'], (string) $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString((string) $row['secret_verifier'], (string) $this->browser->getResponse()->getContent());

        $incomplete = self::getContainer()->get(CandidateSessions::class)->issue($this->fixture['users']['admin'], 'admin', str_repeat('p', 32));
        // Untrusted query fields cannot choose a weaker presentation policy.
        $this->browser->request('GET', $this->url('/account/sessions?requiredStrength=1&auth_strength=2'));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('tr[data-session-id="'.$incomplete.'"] [data-session-strength]', 'Mot de passe');
        self::assertSelectorTextSame('tr[data-session-id="'.$incomplete.'"] [data-session-state]', 'MFA en attente');
        self::assertSelectorTextSame('tr[data-session-id="'.$pending['selector'].'"] [data-session-state]', 'Active');
    }

    public function testRevokedPendingAdministratorCannotConsumeEnrollmentCode(): void
    {
        $pending = $this->pendingAdministrator();
        self::getContainer()->get(CandidateSessions::class)->revoke($this->fixture['users']['admin'], 'admin', $pending['selector']);
        $this->browser->submit($pending['crawler']->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($pending['secret'])->now()]));
        self::assertResponseStatusCodeSame(403);
        $this->assertPendingCodeNotConsumed($pending['selector']);
    }

    public function testPendingEnrollmentAdministratorCannotReadOrRevokeSessions(): void
    {
        $pending = $this->pendingAdministrator();
        $selector = $pending['selector'];
        foreach (['GET', 'POST'] as $method) {
            $this->browser->request($method, $this->url($method === 'GET' ? '/account/sessions' : '/account/sessions/'.$selector.'/revoke'), [
                '_token' => 'forged', 'current_password' => WorkbenchFixture::PASSWORD,
            ]);
            self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
            self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', 'http://admin.localhost/mfa']);
        }
        self::assertNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id = ?', [$selector]));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT auth_strength FROM wcp_candidate_admin_sessions WHERE id = ?', [$selector]));
    }

    public function testAdministratorNeedsFreshMfaBeforeRevokingSessions(): void
    {
        $pending = $this->pendingAdministrator();
        $this->browser->submit($pending['crawler']->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($pending['secret'])->now()]));
        self::assertResponseIsSuccessful();
        $fields = $this->fields($pending['selector']);
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_mfa_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_mfa_proof', $proof);
        $session->save();
        $this->browser->request('POST', $this->url('/account/sessions/'.$pending['selector'].'/revoke'), $fields);
        self::assertResponseStatusCodeSame(403);
        self::assertNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id = ?', [$pending['selector']]));
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseIsSuccessful();
    }

    public function testRevocationBetweenRequestGateAndMfaAttemptIsRecheckedBeforeCodeConsumption(): void
    {
        $pending = $this->pendingAdministrator();
        $sessions = self::getContainer()->get(CandidateSessions::class);
        $actor = $this->fixture['users']['admin'];
        $dispatcher = self::getContainer()->get('event_dispatcher');
        self::assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
        $revoked = false;
        $revokeDuringAttempt = static function () use ($sessions, $actor, $pending, &$revoked): void {
            $revoked = $sessions->revoke($actor, 'admin', $pending['selector']);
        };
        $dispatcher->addListener(TwoFactorAuthenticationEvents::ATTEMPT, $revokeDuringAttempt, 256);
        try {
            $this->browser->submit($pending['crawler']->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($pending['secret'])->now()]));
            self::assertTrue($revoked);
            self::assertResponseStatusCodeSame(403);
            $this->assertPendingCodeNotConsumed($pending['selector']);
        } finally {
            $dispatcher->removeListener(TwoFactorAuthenticationEvents::ATTEMPT, $revokeDuringAttempt);
        }
    }

    private function assertPendingCodeNotConsumed(string $selector): void
    {
        self::assertNull($this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->fixture['users']['admin']]));
        self::assertSame(-1, (int) $this->store->db->fetchOne('SELECT last_counter FROM wcp_candidate_mfa WHERE user_id = ?', [$this->fixture['users']['admin']]));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT auth_strength FROM wcp_candidate_admin_sessions WHERE id = ?', [$selector]));
        self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_admin_sessions WHERE id = ?', [$selector]));
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
    }

    public function testDurableRevocationSurvivesKernelRestart(): void
    {
        $originalDatabaseUrl = (string) $_SERVER['DATABASE_URL'];
        $temporaryDatabase = null;
        try {
            // PostgreSQL is already persistent. Only replace the local in-memory
            // SQLite database, while retaining the real server-side session files.
            if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
                $directory = realpath(dirname(__DIR__, 2).'/var');
                self::assertIsString($directory);
                $temporaryDatabase = tempnam($directory, 'session-restart-');
                self::assertIsString($temporaryDatabase);
                self::assertSame($directory, dirname($temporaryDatabase));
                self::ensureKernelShutdown();
                $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///'.str_replace('\\', '/', $temporaryDatabase);
                $this->bootBrowser('user', true);
            }
            $this->login();
            $target = $this->selector();
            $targetCookies = $this->cookies();
            $this->newBrowserLogin();
            $current = $this->selector();
            $fields = $this->fields($target);
            $currentCookies = $this->cookies();
            $this->browser->request('POST', $this->url('/account/sessions/'.$target.'/revoke'), $fields);
            self::assertResponseRedirects('/account/sessions', 303);
            $this->bootBrowser('user');
            $this->useCookies($targetCookies);
            $this->browser->request('GET', $this->url('/'));
            self::assertResponseRedirects('/login');
            $this->useCookies($currentCookies);
            $this->browser->request('GET', $this->url('/account/sessions'));
            self::assertResponseIsSuccessful();
            self::assertSame($current, $this->selector());
            self::assertNotNull($this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$target]));
            self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
        } finally {
            self::ensureKernelShutdown();
            $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = $originalDatabaseUrl;
            if (is_string($temporaryDatabase) && is_file($temporaryDatabase)) unlink($temporaryDatabase);
        }
    }
}
