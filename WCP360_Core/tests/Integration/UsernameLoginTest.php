<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class UsernameLoginTest extends WebTestCase
{
    public function testLoginUsesHostingWordingAndUsernameAttributes(): void
    {
        $browser = self::createClient([], ['HTTP_HOST' => 'user.localhost']);
        $browser->disableReboot();
        $store = self::getContainer()->get(WorkbenchStore::class);
        WorkbenchFixture::reset($store->db);
        $crawler = $browser->request('GET', 'http://user.localhost/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.auth-eyebrow', 'ESPACE D’HÉBERGEMENT');
        self::assertStringNotContainsString('ESPACE CLIENT', $browser->getResponse()->getContent());
        self::assertSame('text', $crawler->filter('input[name="_username"]')->attr('type'));
        self::assertSame('username', $crawler->filter('input[name="_username"]')->attr('autocomplete'));
        self::assertSame('[a-z][a-z0-9_]{2,31}', $crawler->filter('input[name="_username"]')->attr('pattern'));
        self::assertSame('32', $crawler->filter('input[name="_username"]')->attr('maxlength'));
        $browser->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'alice', '_password' => WorkbenchFixture::PASSWORD,
        ]));
        self::assertResponseRedirects('/');
        $browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.wcp-page-title', 'Hébergement');
        self::assertStringNotContainsString('Espace client', $browser->getResponse()->getContent());
    }

    public function testLegacySessionReturnsToLoginWithoutServerError(): void
    {
        $browser = self::createClient([], ['HTTP_HOST' => 'user.localhost']);
        $browser->disableReboot();
        $fixture = WorkbenchFixture::reset(self::getContainer()->get(WorkbenchStore::class)->db);
        $legacy = (new \ReflectionClass(\Wcp360\Core\Module\IdentityAccess\Domain\Principal::class))->newInstanceWithoutConstructor();
        $legacy->__unserialize(['id' => $fixture['users']['alice'], 'email' => 'alice@example.test',
            'passwordHash' => 'legacy-session-fingerprint', 'context' => 'user', 'active' => true]);
        $browser->loginUser($legacy);
        $browser->request('GET', 'http://user.localhost/');
        self::assertResponseRedirects('/login');
        $browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testEmailWithCorrectPasswordIsRejectedServerSide(): void
    {
        $browser = self::createClient([], ['HTTP_HOST' => 'user.localhost']);
        $browser->disableReboot();
        WorkbenchFixture::reset(self::getContainer()->get(WorkbenchStore::class)->db);
        $crawler = $browser->request('GET', 'http://user.localhost/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $browser->request('POST', 'http://user.localhost/login', [
            '_username' => 'alice@example.test', '_password' => WorkbenchFixture::PASSWORD,
            '_csrf_token' => $form['_csrf_token']->getValue(),
        ]);
        self::assertResponseRedirects('/login');
        $browser->followRedirect();
        self::assertSelectorTextContains('[role="alert"]', 'Connexion refusée');
        $browser->request('GET', 'http://user.localhost/');
        self::assertResponseRedirects('/login');
    }
}
