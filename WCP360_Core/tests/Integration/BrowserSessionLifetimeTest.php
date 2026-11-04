<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Psr\Clock\ClockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Shared\Security\SessionBoundary;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

/** Exercise the browser gate while the independent database registration stays valid. */
final class BrowserSessionLifetimeTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private BrowserSessionTestClock $clock;
    private string $surface = 'user';

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = 'user';
            $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = 'user.localhost';
        }
    }

    /** @return iterable<string, array{string, string, int, int}> */
    public static function lifetimeCases(): iterable
    {
        foreach (['user' => [1800, 43200], 'admin' => [900, 28800]] as $surface => [$idle, $absolute]) {
            foreach (['idle' => $idle, 'absolute' => $absolute] as $kind => $seconds) {
                foreach ([1, 0] as $remaining) {
                    yield $surface.'-'.$kind.'-'.$remaining => [$surface, $kind, $seconds, $remaining];
                }
            }
        }
    }

    #[DataProvider('lifetimeCases')]
    public function testBrowserLifetimeAcceptsBeforeAndRejectsAtExactDeadline(
        string $surface,
        string $kind,
        int $seconds,
        int $remaining,
    ): void {
        $this->bootBrowser($surface);
        $this->login();
        $oldCookies = $this->setBrowserDeadline($kind, $seconds, $remaining);
        $created = $this->browser->getRequest()->getSession()->get('_wcp_created');

        $this->browser->request('GET', $this->url('/account/sessions'));
        if ($remaining === 1) {
            self::assertResponseIsSuccessful();
            self::assertSame($created, $this->browser->getRequest()->getSession()->get('_wcp_created'));
            self::assertSame($this->clock->now()->getTimestamp(), $this->browser->getRequest()->getSession()->get('_wcp_last'));
            self::assertTrue($this->browser->getRequest()->getSession()->has('_wcp_registry'));
            return;
        }

        self::assertResponseRedirects('/login');
        self::assertFalse($this->browser->getRequest()->getSession()->has('_wcp_registry'));
        $this->assertOldCookiesCannotRestoreAccess($oldCookies);
    }

    /** @return iterable<string, array{string, string, int}> */
    public static function expiredMutationCases(): iterable
    {
        foreach (['user' => [1800, 43200], 'admin' => [900, 28800]] as $surface => [$idle, $absolute]) {
            yield $surface.'-idle' => [$surface, 'idle', $idle];
            yield $surface.'-absolute' => [$surface, 'absolute', $absolute];
        }
    }

    #[DataProvider('expiredMutationCases')]
    public function testExpiredBrowserSessionCannotReachAMutation(string $surface, string $kind, int $seconds): void
    {
        $this->bootBrowser($surface);
        $this->login();
        $oldCookies = $this->setBrowserDeadline($kind, $seconds, 0);
        $before = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');

        $this->browser->request('POST', $this->url('/account/sessions/revoke-others'));

        self::assertResponseStatusCodeSame(403);
        self::assertFalse($this->browser->getRequest()->getSession()->has('_wcp_registry'));
        self::assertSame($before, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        $this->assertOldCookiesCannotRestoreAccess($oldCookies);
    }

    private function bootBrowser(string $surface): void
    {
        self::ensureKernelShutdown();
        $this->surface = $surface;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $surface;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $surface.'.localhost';
        $this->browser = self::createClient([], [
            'HTTP_HOST' => $surface.'.localhost',
            'HTTP_ORIGIN' => 'http://'.$surface.'.localhost',
        ]);
        $this->browser->disableReboot();
        $container = self::getContainer();
        $this->store = $container->get(WorkbenchStore::class);
        WorkbenchFixture::reset($this->store->db);
        $this->clock = new BrowserSessionTestClock();
        // Replace only the admission boundary before its first HTTP event.
        // Registry/MFA clocks and persistent registrations remain real and current.
        $container->set(SessionBoundary::class, new SessionBoundary(
            $container->get(RuntimeContext::class),
            $container->get(TokenStorageInterface::class),
            $container->get(CandidateSessions::class),
            $container->get(CandidateMfa::class),
            $this->clock,
        ));
    }

    private function login(): void
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->filter('form')->first()->form([
            '_username' => $this->surface === 'admin' ? 'admin' : 'alice',
            '_password' => WorkbenchFixture::PASSWORD,
        ]));
        for ($attempt = 0; $attempt < 3 && $this->browser->getResponse()->isRedirect(); ++$attempt) {
            $this->browser->followRedirect();
        }
        self::assertResponseIsSuccessful();
        self::assertSame('/', $this->browser->getRequest()->getPathInfo());
    }

    /** @return list<Cookie> */
    private function setBrowserDeadline(string $kind, int $seconds, int $remaining): array
    {
        $session = $this->browser->getRequest()->getSession();
        $registry = $session->get('_wcp_registry');
        self::assertIsArray($registry);
        self::assertIsString($registry['actor'] ?? null);
        self::assertIsString($registry['selector'] ?? null);
        self::assertTrue(self::getContainer()->get(CandidateSessions::class)->current(
            $registry['actor'], $this->surface, $registry['selector'], $session->getId(), 1, false,
        ));

        $this->clock->freeze();
        $now = $this->clock->now()->getTimestamp();
        $deadlineStart = $now - $seconds + $remaining;
        // Only browser timestamps change. The registry's own lifetime is not expired.
        $session->set('_wcp_created', $kind === 'absolute' ? $deadlineStart : $deadlineStart - 60);
        $session->set('_wcp_last', $kind === 'idle' ? $deadlineStart : $now);
        $session->save();

        return $this->browser->getCookieJar()->all();
    }

    /** @param list<Cookie> $cookies */
    private function assertOldCookiesCannotRestoreAccess(array $cookies): void
    {
        $this->browser->getCookieJar()->clear();
        foreach ($cookies as $cookie) {
            $this->browser->getCookieJar()->set($cookie);
        }
        $this->browser->request('GET', $this->url('/account/sessions'));
        self::assertResponseRedirects('/login');
        self::assertFalse($this->browser->getRequest()->getSession()->has('_wcp_registry'));
    }

    private function url(string $path): string
    {
        return 'http://'.$this->surface.'.localhost'.$path;
    }
}

/** Live during login; frozen only for the exact browser-admission assertion. */
final class BrowserSessionTestClock implements ClockInterface
{
    private ?\DateTimeImmutable $frozen = null;

    public function freeze(): void
    {
        $this->frozen = new \DateTimeImmutable();
    }

    public function now(): \DateTimeImmutable
    {
        return $this->frozen ?? new \DateTimeImmutable();
    }
}
