<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class MfaJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private string $admin;
    private string $testKey;

    protected function setUp(): void
    {
        $this->testKey = (string) ($_SERVER['WCP_MFA_KEY'] ?? $_ENV['WCP_MFA_KEY'] ?? '');
        $this->context('admin');
        $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $fixture = WorkbenchFixture::reset($this->store->db);
        $this->admin = $fixture['users']['admin'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->context('user');
        $_ENV['WCP_MFA_KEY'] = $_SERVER['WCP_MFA_KEY'] = $this->testKey;
    }

    private function context(string $context): void
    {
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $context.'.localhost';
    }

    private function login(): Crawler
    {
        $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@example.test',
            '_password' => WorkbenchFixture::PASSWORD,
        ]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); $i++) {
            $crawler = $this->browser->followRedirect();
        }
        self::assertResponseIsSuccessful();
        self::assertSame('/mfa', $this->browser->getRequest()->getPathInfo());

        return $crawler;
    }

    /** @return array{secret: string, code: string, codes: list<string>} */
    private function enroll(): array
    {
        $crawler = $this->login();
        $crawler = $this->browser->submit($crawler->selectButton('Configurer la MFA')->form());
        self::assertResponseIsSuccessful();
        $secret = trim($crawler->filter('#mfa-secret')->text());
        self::assertMatchesRegularExpression('/^[A-Z2-7]{32,128}$/D', $secret);
        $code = TOTP::createFromSecret($secret)->now();
        $crawler = $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $code]));
        self::assertResponseIsSuccessful();
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
        $codes = $crawler->filter('#recovery-codes code')->each(static fn (Crawler $node): string => trim($node->text()));
        self::assertCount(10, $codes);
        self::assertCount(10, array_unique($codes));
        foreach ($codes as $recovery) self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $recovery);

        return ['secret' => $secret, 'code' => $code, 'codes' => $codes];
    }

    private function logout(): void
    {
        $crawler = $this->browser->request('GET', 'http://admin.localhost/');
        self::assertResponseIsSuccessful();
        $this->browser->submit($crawler->filter('form[action="/logout"]')->form());
        self::assertResponseRedirects('/login');
    }

    private function assertPendingMfaRedirect(): void
    {
        self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
        $location = $this->browser->getResponse()->headers->get('Location');
        self::assertContains($location, ['/mfa', 'http://admin.localhost/mfa']);
    }

    public function testPasswordAloneCannotReadDashboardOrMutateAdminFeatures(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/');
        $this->assertPendingMfaRedirect();
        foreach (['/workbench/settings/application-server', '/workbench/plugins/install'] as $path) {
            $this->browser->request('POST', 'http://admin.localhost'.$path, ['_token' => 'forged']);
            $this->assertPendingMfaRedirect();
        }
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT revision FROM wcp_candidate_runtime_settings'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_plugins'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa'));
    }

    public function testEnrollmentAndChallengeRejectOriginAndCsrfBeforeConsumingCode(): void
    {
        $crawler = $this->login();
        $enroll = $crawler->selectButton('Configurer la MFA')->form();
        $fields = $enroll->getValues();
        $this->browser->request('POST', 'http://admin.localhost/mfa/enroll', array_replace($fields, ['_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', 'http://admin.localhost/mfa/enroll', $fields, [], ['HTTP_ORIGIN' => 'https://foreign.example']);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', 'http://admin.localhost/mfa/enroll', $fields, [], ['HTTP_ORIGIN' => '']);
        self::assertResponseStatusCodeSame(403);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa'));
        $crawler = $this->browser->request('POST', 'http://admin.localhost/mfa/enroll', $fields);
        self::assertResponseIsSuccessful();
        $secret = trim($crawler->filter('#mfa-secret')->text());
        $challenge = $crawler->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($secret)->now()]);
        $challengeFields = $challenge->getValues();
        $this->browser->request('POST', 'http://admin.localhost/mfa/check', $challengeFields, [], ['HTTP_ORIGIN' => 'https://foreign.example']);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', 'http://admin.localhost/mfa/check', array_replace($challengeFields, ['_csrf_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        self::assertNull($this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]));
        self::assertSame(-1, (int) $this->store->db->fetchOne('SELECT last_counter FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]));
        $this->browser->request('POST', 'http://admin.localhost/mfa/check', $challengeFields);
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, '#recovery-codes code');
    }

    public function testEnrollmentAllowsAdminWithoutPersistingCleartextSecretsInSessionOrAudit(): void
    {
        $crawler = $this->login();
        $initialSession = $this->browser->getRequest()->getSession()->getId();
        $crawler = $this->browser->submit($crawler->selectButton('Configurer la MFA')->form());
        $secret = trim($crawler->filter('#mfa-secret')->text());
        $crawler = $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($secret)->now()]));
        self::assertResponseIsSuccessful();
        self::assertNotSame($initialSession, $this->browser->getRequest()->getSession()->getId());
        $codes = $crawler->filter('#recovery-codes code')->each(static fn (Crawler $node): string => trim($node->text()));
        self::assertCount(10, $codes);
        $session = serialize($this->browser->getRequest()->getSession()->all());
        $principal = self::getContainer()->get(TokenStorageInterface::class)->getToken()?->getUser();
        self::assertNotNull($principal);
        $serialized = serialize($principal);
        $audit = json_encode($this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'), JSON_THROW_ON_ERROR);
        foreach (array_merge([$this->testKey, $secret], $codes) as $sensitive) {
            self::assertNotSame('', $sensitive);
            self::assertStringNotContainsString($sensitive, $session);
            self::assertStringNotContainsString($sensitive, $serialized);
            self::assertStringNotContainsString($sensitive, $audit);
        }
        $this->browser->request('GET', 'http://admin.localhost/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#default-application-server');
        self::assertSelectorNotExists('#recovery-codes');
        $this->browser->request('GET', 'http://admin.localhost/account/security');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('#mfa-secret');
        self::assertSelectorNotExists('#recovery-codes');
    }

    public function testTotpReplayIsRejectedAndRecoveryCodeIsSingleUse(): void
    {
        $factor = $this->enroll();
        $this->logout();
        $crawler = $this->login();
        self::assertSelectorNotExists('#mfa-secret');
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $factor['code']]));
        $this->assertPendingMfaRedirect();
        $crawler = $this->browser->followRedirect();
        $before = self::getContainer()->get(CandidateMfa::class)->status($this->admin)['revision'];
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $factor['codes'][0]]));
        self::assertResponseRedirects('/');
        self::assertSame($before + 1, self::getContainer()->get(CandidateMfa::class)->status($this->admin)['revision']);
        self::assertSame(9, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery WHERE user_id = ?', [$this->admin]));
        $this->logout();
        $crawler = $this->login();
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $factor['codes'][0]]));
        $this->assertPendingMfaRedirect();
        $this->browser->request('GET', 'http://admin.localhost/');
        $this->assertPendingMfaRedirect();
        self::assertSame(9, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery WHERE user_id = ?', [$this->admin]));
    }

    public function testRecoveryElsewhereInvalidatesAnExistingAdminMfaProof(): void
    {
        $factor = $this->enroll();
        $result = self::getContainer()->get(CandidateMfa::class)->check($this->admin, $factor['codes'][0]);
        self::assertSame('recovery', $result['method'] ?? null);
        $this->browser->request('GET', 'http://admin.localhost/');
        self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', '/login']);
    }

    public function testDisablingAdminInvalidatesAnExistingMfaSession(): void
    {
        $this->enroll();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->admin]);
        $this->browser->request('GET', 'http://admin.localhost/');
        self::assertResponseRedirects('/login');
    }

    public function testExpiredPendingAuthenticationCannotConfirmACurrentCode(): void
    {
        $crawler = $this->login();
        $crawler = $this->browser->submit($crawler->selectButton('Configurer la MFA')->form());
        $secret = trim($crawler->filter('#mfa-secret')->text());
        $form = $crawler->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($secret)->now()]);
        $session = $this->browser->getRequest()->getSession();
        $pending = $session->get('_wcp_mfa_pending');
        self::assertIsArray($pending);
        $pending['started'] = time() - 301;
        $session->set('_wcp_mfa_pending', $pending);
        $session->save();
        $this->browser->submit($form);
        self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303, 403]);
        self::assertNull($this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]));
        self::assertSame(-1, (int) $this->store->db->fetchOne('SELECT last_counter FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
    }

    public function testExpiredFreshMfaProofCannotMutateAdministratorSettings(): void
    {
        $this->enroll();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_mfa_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_mfa_proof', $proof);
        $session->save();
        $crawler = $this->browser->request('GET', 'http://admin.localhost/');
        self::assertResponseIsSuccessful();
        $form = $crawler->filter('form[action="/workbench/settings/application-server"]')->form();
        $this->browser->submit($form);
        self::assertResponseStatusCodeSame(403);
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT revision FROM wcp_candidate_runtime_settings'));
    }

    public function testRecoveryThenPasswordConfirmedResetRequiresFreshEnrollment(): void
    {
        $factor = $this->enroll();
        $this->logout();
        $crawler = $this->login();
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $factor['codes'][0]]));
        self::assertResponseRedirects('/');
        $crawler = $this->browser->request('GET', 'http://admin.localhost/account/security');
        self::assertResponseIsSuccessful();
        $form = $crawler->selectButton('Réinitialiser mon facteur')->form(['current_password' => WorkbenchFixture::PASSWORD]);
        $fields = $form->getValues();
        $before = self::getContainer()->get(CandidateMfa::class)->status($this->admin)['revision'];
        foreach ([['HTTP_ORIGIN' => 'https://foreign.example'], ['HTTP_ORIGIN' => '']] as $server) {
            $this->browser->request('POST', 'http://admin.localhost/mfa/reset', $fields, [], $server);
            self::assertResponseStatusCodeSame(403);
        }
        $this->browser->request('POST', 'http://admin.localhost/mfa/reset', array_replace($fields, ['_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', 'http://admin.localhost/mfa/reset', array_replace($fields, ['current_password' => 'incorrect-current-password']));
        self::assertResponseStatusCodeSame(403);
        self::assertTrue(self::getContainer()->get(CandidateMfa::class)->status($this->admin)['enabled']);
        self::assertSame($before, self::getContainer()->get(CandidateMfa::class)->status($this->admin)['revision']);
        $this->browser->request('POST', 'http://admin.localhost/mfa/reset', $fields);
        self::assertResponseRedirects('/login');
        $status = self::getContainer()->get(CandidateMfa::class)->status($this->admin);
        self::assertFalse($status['enabled']);
        self::assertSame($before + 1, $status['revision']);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        $crawler = $this->login();
        self::assertSame(1, $crawler->selectButton('Configurer la MFA')->count());
        self::assertSelectorNotExists('#mfa-secret');
        $this->browser->request('GET', 'http://admin.localhost/');
        $this->assertPendingMfaRedirect();
    }

    public function testMissingEncryptionKeyBlocksAdminWithoutBreakingUserAuthentication(): void
    {
        self::ensureKernelShutdown();
        $_ENV['WCP_MFA_KEY'] = $_SERVER['WCP_MFA_KEY'] = '';
        $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
        $this->browser->disableReboot();
        $store = self::getContainer()->get(WorkbenchStore::class);
        WorkbenchFixture::reset($store->db);
        $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'admin@example.test', '_password' => WorkbenchFixture::PASSWORD]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); $i++) $this->browser->followRedirect();
        self::assertResponseStatusCodeSame(503);
        self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, (string) $this->browser->getResponse()->getContent());
        self::ensureKernelShutdown();
        $this->context('user');
        $this->browser = self::createClient([], ['HTTP_HOST' => 'user.localhost', 'HTTP_ORIGIN' => 'http://user.localhost']);
        $this->browser->disableReboot();
        $store = self::getContainer()->get(WorkbenchStore::class);
        WorkbenchFixture::reset($store->db);
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice@example.test', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('select[name="tenant_id"]');
        self::assertSelectorNotExists('#recovery-codes');
    }

    /** @return iterable<string, array{string}> */
    public static function invalidatedEncryptionKeys(): iterable
    {
        yield 'removed key' => [''];
        yield 'different correctly formatted key' => [str_repeat('ab', 32)];
    }

    #[DataProvider('invalidatedEncryptionKeys')]
    public function testChangedEncryptionKeyInvalidatesAnExistingMfaSessionWithoutBlockingUsers(string $changedKey): void
    {
        $originalDatabaseUrl = (string) $_SERVER['DATABASE_URL'];
        $temporaryDatabase = null;
        if ($changedKey === $this->testKey) $changedKey = str_repeat('cd', 32);
        try {
            // The identity and encrypted factor must survive an actual process-container
            // restart. PostgreSQL is already persistent; use a private Core-local file
            // only for the otherwise in-memory SQLite test connection.
            if ($this->store->db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) {
                $directory = realpath(dirname(__DIR__, 2).'/var');
                self::assertIsString($directory);
                $temporaryDatabase = tempnam($directory, 'mfa-key-change-');
                self::assertIsString($temporaryDatabase);
                self::assertSame($directory, dirname($temporaryDatabase));
                self::ensureKernelShutdown();
                $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///'.str_replace('\\', '/', $temporaryDatabase);
                $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
                $this->browser->disableReboot();
                $this->store = self::getContainer()->get(WorkbenchStore::class);
                $fixture = WorkbenchFixture::reset($this->store->db);
                $this->admin = $fixture['users']['admin'];
            }

            $this->enroll();
            $sealed = $this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]);
            self::assertIsString($sealed);
            $cookies = $this->browser->getCookieJar()->all();
            self::assertNotEmpty($cookies);
            self::ensureKernelShutdown();
            $_ENV['WCP_MFA_KEY'] = $_SERVER['WCP_MFA_KEY'] = $changedKey;
            $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
            $this->browser->disableReboot();
            foreach ($cookies as $cookie) $this->browser->getCookieJar()->set($cookie);
            $this->store = self::getContainer()->get(WorkbenchStore::class);
            $status = self::getContainer()->get(CandidateMfa::class)->status($this->admin);
            self::assertTrue($status['enabled']);
            self::assertFalse($status['configured']);
            $this->browser->request('GET', 'http://admin.localhost/');
            self::assertResponseRedirects('/login');
            $this->browser->request('GET', 'http://admin.localhost/account/security');
            self::assertResponseRedirects('/login');
            self::assertSame($sealed, $this->store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]));
            self::assertSame(1, (int) $this->store->db->fetchOne('SELECT active FROM wcp_candidate_users WHERE id = ?', [$this->admin]));

            self::ensureKernelShutdown();
            $this->context('user');
            $this->browser = self::createClient([], ['HTTP_HOST' => 'user.localhost', 'HTTP_ORIGIN' => 'http://user.localhost']);
            $this->browser->disableReboot();
            $crawler = $this->browser->request('GET', 'http://user.localhost/login');
            $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice@example.test', '_password' => WorkbenchFixture::PASSWORD]));
            self::assertResponseRedirects('/');
            $this->browser->followRedirect();
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('select[name="tenant_id"]');
        } finally {
            self::ensureKernelShutdown();
            $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = $originalDatabaseUrl;
            if (is_string($temporaryDatabase) && is_file($temporaryDatabase)) unlink($temporaryDatabase);
        }
    }
}
