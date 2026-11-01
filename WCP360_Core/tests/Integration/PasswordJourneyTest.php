<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\ChangeOwnPassword;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PasswordJourneyTest extends WebTestCase
{
    private const NEXT = 'next-synthetic-password-only-2026';
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private string $surface = 'user';

    protected function setUp(): void
    {
        $this->bootBrowser('user');
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = 'user';
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = 'user.localhost';
    }

    private function bootBrowser(string $surface): void
    {
        self::ensureKernelShutdown();
        $this->surface = $surface;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $surface;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $surface.'.localhost';
        $this->browser = self::createClient([], ['HTTP_HOST' => $surface.'.localhost', 'HTTP_ORIGIN' => 'http://'.$surface.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
    }

    private function url(string $path): string { return 'http://'.$this->surface.'.localhost'.$path; }

    private function login(string $password = WorkbenchFixture::PASSWORD): Crawler
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form([
            '_username' => $this->surface === 'admin' ? 'admin' : 'alice', '_password' => $password,
        ]));
        self::assertResponseRedirects('/');
        return $this->browser->followRedirect();
    }

    private function fields(): array
    {
        $crawler = $this->browser->request('GET', $this->url('/account/password'));
        self::assertResponseIsSuccessful();
        $fields = $crawler->selectButton('Changer le mot de passe et me déconnecter')->form()->getValues();
        return array_replace($fields, ['current_password' => WorkbenchFixture::PASSWORD,
            'new_password' => self::NEXT, 'repeat_password' => self::NEXT]);
    }

    private function hash(string $name = 'alice'): string
    {
        return $this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE username=? AND context=?', [$name, $this->surface]);
    }

    public function testAnonymousCannotUsePasswordPageOrMutation(): void
    {
        $this->browser->request('GET', $this->url('/account/password'));
        self::assertResponseRedirects('/login');
        $this->browser->request('POST', $this->url('/account/password'), ['new_password' => self::NEXT]);
        self::assertResponseRedirects('/login');
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testFormHasNoMutationOnGetAndSuccessRequiresNewLogin(): void
    {
        $this->login();
        $before = $this->hash();
        $audit = (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit');
        $fields = $this->fields();
        self::assertSame($before, $this->hash());
        self::assertSame($audit, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
        self::assertStringContainsString('no-store', $this->browser->getResponse()->headers->get('Cache-Control'));
        self::assertStringContainsString('private', $this->browser->getResponse()->headers->get('Cache-Control'));
        self::assertSelectorExists('meta[name="turbo-cache-control"][content="no-cache"]');
        self::assertSelectorExists('a[aria-current="page"][href="/account/password"]');
        self::assertSelectorCount(2, 'input[autocomplete="new-password"]');
        self::assertSelectorExists('form[data-turbo-frame="_top"]');
        self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString($before, $this->browser->getResponse()->getContent());
        $this->browser->request('POST', $this->url('/account/password'), $fields);
        self::assertResponseRedirects('/login', 303);
        self::assertTrue(password_verify(self::NEXT, $this->hash()));
        self::assertFalse(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/login');
        $this->login(self::NEXT);
        self::assertResponseIsSuccessful();
    }

    public function testAnotherPreviouslyAuthenticatedBrowserIsRevoked(): void
    {
        $this->login();
        $session = $this->browser->getRequest()->getSession();
        if ($session->isStarted()) $session->save();
        /** @var list<Cookie> $oldCookies */
        $oldCookies = $this->browser->getCookieJar()->all();
        $this->browser->getCookieJar()->clear();
        $this->login();
        $this->browser->request('POST', $this->url('/account/password'), $this->fields());
        self::assertResponseRedirects('/login', 303);
        foreach ($oldCookies as $cookie) $this->browser->getCookieJar()->set($cookie);
        $this->browser->request('GET', $this->url('/'));
        self::assertResponseRedirects('/login');
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
    }

    public function testOriginCsrfAndForgedTargetAreRefusedWithoutConsumingBudget(): void
    {
        $this->login();
        $fields = $this->fields();
        $before = $this->hash();
        foreach (['', 'null', 'https://foreign.example'] as $origin) {
            $this->browser->request('POST', $this->url('/account/password'), $fields, [], ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString(self::NEXT, $this->browser->getResponse()->getContent());
            self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
        }
        $this->browser->request('POST', $this->url('/account/password'), array_replace($fields, ['_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', $this->url('/account/password'), $fields + ['actor' => $this->fixture['users']['bob']]);
        self::assertResponseStatusCodeSame(400);
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions'));
    }

    public function testWrongCurrentPasswordUsesTheSharedDurableBudget(): void
    {
        $this->login();
        $fields = array_replace($this->fields(), ['current_password' => 'wrong-synthetic-password']);
        $before = $this->hash();
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->browser->request('POST', $this->url('/account/password'), $fields);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString(self::NEXT, $this->browser->getResponse()->getContent());
            self::assertStringNotContainsString('wrong-synthetic-password', $this->browser->getResponse()->getContent());
        }
        $crawler = $this->browser->request('GET', $this->url('/account/sessions'));
        $this->browser->submit($crawler->filter('form[action="/account/sessions/revoke-all"]')->form(['current_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseStatusCodeSame(429);
        self::assertSame(5, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions'));
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_user_sessions WHERE revoked_at IS NOT NULL'));
    }

    public function testMalformedNewPasswordsAndMismatchedConfirmationNeverEchoSecrets(): void
    {
        $this->login();
        $fields = $this->fields();
        $before = $this->hash();
        foreach ([
            ['new_password' => 'too-short', 'repeat_password' => 'too-short'],
            ['new_password' => str_repeat('é', 7), 'repeat_password' => str_repeat('é', 7)],
            ['repeat_password' => 'different-synthetic-password'],
        ] as $override) {
            $this->browser->request('POST', $this->url('/account/password'), array_replace($fields, $override));
            self::assertResponseStatusCodeSame(422);
            self::assertSelectorExists('[role="alert"]');
            self::assertSelectorCount(3, 'input[type="password"]:not([value]), input[type="password"][value=""]');
            self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
            self::assertStringNotContainsString(self::NEXT, $this->browser->getResponse()->getContent());
        }
        foreach ([['new_password' => ['nested']], ['new_password' => str_repeat('x', 257)], ['repeat_password' => ['nested']]] as $override) {
            $this->browser->request('POST', $this->url('/account/password'), array_replace($fields, $override));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
        }
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions'));
    }

    public function testReusingCurrentPasswordIsRejectedAfterConfirmation(): void
    {
        $this->login();
        $fields = array_replace($this->fields(), ['new_password' => WorkbenchFixture::PASSWORD, 'repeat_password' => WorkbenchFixture::PASSWORD]);
        $before = $this->hash();
        $this->browser->request('POST', $this->url('/account/password'), $fields);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('[role="alert"]', 'différent');
        self::assertSame($before, $this->hash());
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions'));
    }

    public function testPasswordOnlyAdministratorCanChangeOwnPassword(): void
    {
        $this->bootBrowser('admin');
        WorkbenchFixture::reset($this->store->db);
        $this->login();
        $this->browser->request('POST', $this->url('/account/password'), $this->fields());
        self::assertResponseRedirects('/login', 303);
        self::assertTrue(password_verify(self::NEXT, $this->hash('admin')));
        self::assertFalse(self::getContainer()->get(CandidateMfa::class)->status($this->store->db->fetchOne("SELECT id FROM wcp_candidate_users WHERE username='admin'"))['enabled']);
        $this->login(self::NEXT);
        self::assertResponseIsSuccessful();
    }

    public function testEnabledMfaRemainsRequiredAfterPasswordChange(): void
    {
        $this->bootBrowser('admin');
        $fixture = WorkbenchFixture::reset($this->store->db);
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $enrollment = $mfa->startEnrollment($fixture['users']['admin']);
        self::assertNotNull($mfa->check($fixture['users']['admin'], TOTP::createFromSecret($enrollment['secret'])->now(), 1));
        // The next accepted counter avoids waiting or weakening TOTP replay protection.
        $nextCode = TOTP::createFromSecret($enrollment['secret'])->at(time()+30);
        $this->login();
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider')->form(['_auth_code' => $nextCode]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); $i++) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
        $this->browser->request('POST', $this->url('/account/password'), $this->fields());
        self::assertResponseRedirects('/login', 303);
        self::assertTrue($mfa->status($fixture['users']['admin'])['enabled']);
        self::assertSame(10, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_mfa_recovery'));
        $this->login(self::NEXT);
        self::assertResponseRedirects('/mfa');
    }

    public function testPendingMfaCannotReachPasswordChange(): void
    {
        $this->bootBrowser('admin');
        $fixture = WorkbenchFixture::reset($this->store->db);
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $enrollment = $mfa->startEnrollment($fixture['users']['admin']);
        self::assertNotNull($mfa->check($fixture['users']['admin'], TOTP::createFromSecret($enrollment['secret'])->now(), 1));
        $before = $this->hash('admin');
        $this->login();
        $this->browser->request('GET', $this->url('/account/password'));
        self::assertResponseRedirects('/mfa');
        $this->browser->request('POST', $this->url('/account/password'), ['_token' => 'forged', 'current_password' => WorkbenchFixture::PASSWORD,
            'new_password' => self::NEXT, 'repeat_password' => self::NEXT]);
        self::assertResponseRedirects('/mfa');
        self::assertSame($before, $this->hash('admin'));
    }

    public function testStaleAdministratorProofCannotChangePassword(): void
    {
        $this->bootBrowser('admin');
        WorkbenchFixture::reset($this->store->db);
        $this->login();
        $fields = $this->fields();
        $before = $this->hash('admin');
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        $proof['at'] = time()-300;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->browser->request('POST', $this->url('/account/password'), $fields);
        self::assertResponseStatusCodeSame(403);
        self::assertSame($before, $this->hash('admin'));
        self::assertStringNotContainsString(self::NEXT, $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_admin_sessions'));
    }

    public function testIsolatedModeHasNoFormAndPostStaysUnavailable(): void
    {
        $this->login();
        $service = new ChangeOwnPassword($this->store, self::getContainer()->get(CandidateSessions::class),
            self::getContainer()->get(CandidateMfa::class), self::getContainer()->get(UserPasswordHasherInterface::class),
            new RuntimeContext('user', true, 'test', 'user.localhost'), 'session-isolated');
        self::getContainer()->set(ChangeOwnPassword::class, $service);
        $this->browser->request('GET', $this->url('/account/password'));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[role="status"]', 'indisponible');
        self::assertSelectorNotExists('input[name="new_password"]');
        $before = $this->hash();
        $this->browser->request('POST', $this->url('/account/password'), ['_token' => 'forged',
            'current_password' => WorkbenchFixture::PASSWORD, 'new_password' => self::NEXT, 'repeat_password' => self::NEXT]);
        self::assertResponseStatusCodeSame(503);
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT reauth_attempts FROM wcp_candidate_user_sessions'));
    }
}
