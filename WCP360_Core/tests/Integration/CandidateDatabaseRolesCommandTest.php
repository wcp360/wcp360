<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CandidateDatabaseRolesCommandTest extends KernelTestCase
{
    private array $saved = [];

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        foreach ($this->saved as $name => [$server, $env]) {
            if ($server === null) { unset($_SERVER[$name]); } else { $_SERVER[$name] = $server; }
            if ($env === null) { unset($_ENV[$name]); } else { $_ENV[$name] = $env; }
        }
        parent::tearDown();
    }

    #[DataProvider('ordinaryContexts')]
    public function testOrdinaryRuntimeCannotInspectOrApplyRoles(string $context): void
    {
        $this->set('WCP_CONTEXT', $context);
        $this->set('WCP_CANDIDATE_DATABASE_USER_PASSWORD', 'private-never-display-marker');
        $kernel = self::bootKernel();
        $tester = new CommandTester((new Application($kernel))->find('wcp:candidate:database:roles'));
        foreach ([[], ['--apply' => true], ['--audit-reads' => true], ['--audit-reads' => true, '--apply' => true]] as $arguments) {
            self::assertSame(2, $tester->execute($arguments, ['interactive' => false]));
            $result = json_decode(trim($tester->getDisplay()), true, 16, JSON_THROW_ON_ERROR);
            self::assertSame('refused', $result['status']);
            self::assertFalse($result['production_qualified']);
            self::assertStringNotContainsString('private-never-display-marker', $tester->getDisplay());
        }
    }

    public static function ordinaryContexts(): iterable
    {
        foreach (['user', 'admin', 'worker'] as $context) {
            yield [$context];
        }
    }

    public function testSetupApplyIsDisabledByDefaultBeforeReadingSecretsOrUsingTheDatabase(): void
    {
        $this->set('WCP_CONTEXT', 'setup');
        $this->set('WCP_ALLOW_CANDIDATE_DATABASE_ROLES', '0');
        $this->set('WCP_CANDIDATE_DATABASE_USER_PASSWORD', 'private-never-display-marker');
        $kernel = self::bootKernel();
        $tester = new CommandTester((new Application($kernel))->find('wcp:candidate:database:roles'));
        self::assertSame(2, $tester->execute(['--audit-reads' => true, '--apply' => true], ['interactive' => false]));
        self::assertSame(2, $tester->execute(['--apply' => true], ['interactive' => false]));
        self::assertSame('refused', json_decode(trim($tester->getDisplay()), true, 16, JSON_THROW_ON_ERROR)['status']);
        self::assertStringNotContainsString('private-never-display-marker', $tester->getDisplay());
    }

    private function set(string $name, string $value): void
    {
        $this->saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null];
        $_SERVER[$name] = $_ENV[$name] = $value;
    }
}
