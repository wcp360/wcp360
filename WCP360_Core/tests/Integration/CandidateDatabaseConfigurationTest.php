<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Shared\Security\CandidateDatabaseUrl;

final class CandidateDatabaseConfigurationTest extends KernelTestCase
{
    private array $saved = [];

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        foreach ($this->saved as $name => [$server, $env]) {
            if ($server === null) {
                unset($_SERVER[$name]);
            } else {
                $_SERVER[$name] = $server;
            }
            if ($env === null) {
                unset($_ENV[$name]);
            } else {
                $_ENV[$name] = $env;
            }
        }
        parent::tearDown();
    }

    public function testSymfonySelectsTheContextUrlWithoutUsingSharedCredentials(): void
    {
        $this->set('WCP_DATABASE_MODE', 'session-isolated');
        $this->set('WCP_DATABASE_USER_URL', 'postgresql://selected-user:selected%25password@127.0.0.1:1/wcp360_test?serverVersion=17');
        $this->set('DATABASE_URL', 'sqlite:///:memory:');
        self::bootKernel();
        $params = self::getContainer()->get(Connection::class)->getParams();
        self::assertSame('pdo_pgsql', $params['driver']);
        self::assertSame('selected-user', $params['user']);
        self::assertSame('selected%password', $params['password']);
        self::assertSame('wcp360_test', $params['dbname']);
    }

    public function testMissingContextUrlDoesNotOpenTheSharedDatabase(): void
    {
        $this->set('WCP_DATABASE_MODE', 'session-isolated');
        $this->set('WCP_DATABASE_USER_URL', '');
        self::bootKernel();
        $this->expectExceptionMessage(CandidateDatabaseUrl::REJECTED);
        self::getContainer()->get(Connection::class);
    }

    private function set(string $name, string $value): void
    {
        $this->saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null];
        $_SERVER[$name] = $_ENV[$name] = $value;
    }
}
