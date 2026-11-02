<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Browser;

use OTPHP\TOTP;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\PantherTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class AdministratorIdentityJourneyTest extends PantherTestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAdministratorPasswordMfaReconfirmationRevocationAndLogout(): void
    {
        $values = [
            'DATABASE_URL' => 'sqlite:///'.str_replace('\\', '/', dirname(__DIR__, 2).'/var/panther-admin.db'),
            'APP_ENV' => 'test', 'APP_DEBUG' => '1', 'APP_SECRET' => 'browser-admin-synthetic-secret',
            'WCP_CANDIDATE' => '1', 'WCP_CONTEXT' => 'admin', 'WCP_PUBLIC_HOST' => 'localhost',
            'WCP_MFA_KEY' => bin2hex(random_bytes(32)), 'WCP_DATABASE_MODE' => 'shared',
            'WCP_ALLOW_FRANKENPHP_PLANS' => '0', 'WCP_ALLOW_ISOLATED_PROVISIONING' => '0',
            'WCP_ALLOW_CANDIDATE_DATABASE_ROLES' => '0',
        ];
        foreach ($values as $name => $value) {
            $_ENV[$name] = $_SERVER[$name] = $value;
            putenv($name.'='.$value);
        }
        $_SERVER['PANTHER_APP_ENV'] = 'dev';
        $client = self::createPantherClient([
            'browser' => self::CHROME, 'hostname' => 'localhost', 'port' => 9081,
            'webServerDir' => dirname(__DIR__, 2).'/public',
        ], [], ['chromedriver_arguments' => ['--log-level=WARNING', '--log-path=/tmp/wcp-browser/admin-chromedriver.log']]);
        $store = self::getContainer()->get(WorkbenchStore::class);
        WorkbenchFixture::reset($store->db);
        $client->request('GET', '/login');
        $client->waitFor('[data-appearance-ready="true"]');
        $client->submitForm('Se connecter', ['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('.wcp-dashboard-sidebar');
        self::assertSelectorTextContains('h1', 'Tableau de bord');
        self::assertSelectorNotExists('#auth-code');
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa'));
        $client->clickLink('Sécurité du compte');
        $client->waitFor('a[href="/account/security/mfa/enable"]');
        self::assertSelectorTextContains('[role="status"]', 'MFA désactivée');
        $client->clickLink('Configurer la MFA');
        $client->waitFor('form[action="/mfa/enroll"]');
        $client->submitForm('Configurer la MFA', ['current_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('#mfa-secret');
        $secret = trim($client->getCrawler()->filter('#mfa-secret')->text());
        $code = TOTP::createFromSecret($secret)->now();
        $client->submitForm('Valider la MFA', ['_auth_code' => $code]);
        $client->waitFor('#recovery-codes code');
        self::assertSelectorCount(10, '#recovery-codes code');
        self::assertSelectorExists('meta[name="turbo-cache-control"][content="no-cache"]');
        $backup = $client->getCrawler()->filter('#recovery-codes code')->each(static fn (Crawler $node): string => trim($node->text()));
        $client->clickLink('Ouvrir le panneau');
        $client->waitFor('.wcp-dashboard-sidebar');
        self::assertSelectorCount(1, '.wcp-nav a[href="/account/reauthenticate"]');
        $client->executeScript("window.wcpReauthJourney='turbo'; document.querySelector('.wcp-nav a[href=\"/account/reauthenticate\"]').click();");
        $client->waitFor('#reauth-password');
        self::assertSame('turbo', $client->executeScript('return window.wcpReauthJourney'));
        self::assertSelectorTextContains('h1', 'Confirmer votre identité');
        self::assertSelectorExists('input[autocomplete="current-password"]');
        $client->submitForm('Confirmer mon mot de passe', ['_password' => 'incorrect-synthetic-password']);
        $client->waitFor('[role="alert"]');
        self::assertSelectorTextContains('[role="alert"]', 'Confirmation refusée');
        self::assertSame('', $client->executeScript('return document.querySelector("#reauth-password").value'));
        $client->submitForm('Confirmer mon mot de passe', ['_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('#auth-code');
        self::assertSelectorNotExists('.wcp-dashboard-sidebar');
        self::assertSelectorNotExists('#mfa-secret');
        $client->submitForm('Valider la MFA', ['_auth_code' => $code]);
        $client->waitFor('[role="alert"]');
        self::assertSelectorTextContains('[role="alert"]', 'Vérification refusée');
        $client->submitForm('Valider la MFA', ['_auth_code' => $backup[0]]);
        $client->waitFor('.wcp-dashboard-sidebar');
        self::assertSelectorTextContains('h1', 'Tableau de bord');
        self::assertSame(1, (int) $store->db->fetchOne('SELECT revision FROM wcp_candidate_runtime_settings'));
        self::assertSame(3, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
        self::assertSame(1, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NULL'));
        $client->clickLink('Mes sessions');
        $client->waitFor('form[action="/account/sessions/revoke-all"]');
        $current = $store->db->fetchOne('SELECT id FROM wcp_candidate_admin_sessions WHERE revoked_at IS NULL');
        self::assertIsString($current);
        self::assertSelectorTextContains('tr[data-session-id="'.$current.'"] [data-session-strength]', 'Mot de passe + MFA');
        self::assertSelectorTextContains('tr[data-session-id="'.$current.'"] [data-session-state]', 'Active');
        $client->submitForm('Tout déconnecter', ['current_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('#password');
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NULL'));
        $client->submitForm('Se connecter', ['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('#auth-code');
        $client->submitForm('Valider la MFA', ['_auth_code' => $backup[1]]);
        $client->waitFor('.wcp-dashboard-sidebar');
        $client->clickLink('Sécurité du compte');
        $client->waitFor('form[action="/mfa/reset"]');
        $client->submitForm('Désactiver la MFA', ['current_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('#password');
        self::assertNull($store->db->fetchOne('SELECT sealed_secret FROM wcp_candidate_mfa'));
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        $client->submitForm('Se connecter', ['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]);
        $client->waitFor('.wcp-dashboard-sidebar');
        self::assertSelectorNotExists('#auth-code');
        $client->executeScript('document.querySelector(".wcp-core-logout button").click();');
        $client->waitFor('#password');
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NULL'));
    }
}
