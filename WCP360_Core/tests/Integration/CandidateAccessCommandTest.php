<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class CandidateAccessCommandTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = 'user';
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = 'user.localhost';
    }

    /** @return array{CommandTester, WorkbenchStore, array{users: array<string, string>, tenants: array<string, string>}} */
    private function command(string $context): array
    {
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = in_array($context, ['user', 'admin'], true) ? $context.'.localhost' : 'localhost';
        $kernel = self::bootKernel();
        $store = self::getContainer()->get(WorkbenchStore::class);
        $fixture = WorkbenchFixture::reset($store->db);
        $application = new Application($kernel);

        return [new CommandTester($application->find('wcp:candidate:access:check')), $store, $fixture];
    }

    /** @return iterable<string, array{string}> */
    public static function operatorContexts(): iterable
    {
        yield 'worker' => ['worker'];
        yield 'setup' => ['setup'];
    }

    /** @return array<string, mixed> */
    private function commandResult(CommandTester $command): array
    {
        $result = json_decode(trim($command->getDisplay()), true, 16, JSON_THROW_ON_ERROR);
        self::assertIsArray($result);
        self::assertSame('wcp360.candidate.access-check/1', $result['schema'] ?? null);
        self::assertFalse($result['production'] ?? null);
        self::assertIsBool($result['allowed'] ?? null);

        return $result;
    }

    #[DataProvider('operatorContexts')]
    public function testOperatorCommandReadsCurrentDenialWithoutMutatingAuthority(string $context): void
    {
        [$command, $store, $fixture] = $this->command($context);
        $arguments = ['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['alice']];
        $auditBefore = (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        self::assertSame(0, $command->execute($arguments));
        self::assertTrue($this->commandResult($command)['allowed']);
        self::assertSame($auditBefore, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));

        $administration = self::getContainer()->get(CandidatePermissionAdministration::class);
        $administration->setDenied($fixture['users']['admin'], $fixture['users']['alice'], $fixture['tenants']['alice'], true);
        $auditAfterDenial = (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        self::assertSame(1, $command->execute($arguments));
        self::assertFalse($this->commandResult($command)['allowed']);
        self::assertSame(1, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));
        self::assertSame($auditAfterDenial, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame(2, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships'));

        $administration->setDenied($fixture['users']['admin'], $fixture['users']['alice'], $fixture['tenants']['alice'], false);
        self::assertSame(0, $command->execute($arguments));
        self::assertTrue($this->commandResult($command)['allowed']);
    }

    public function testCommandRejectsCrossTenantInactiveAdminAndUnknownPermissions(): void
    {
        [$command, $store, $fixture] = $this->command('worker');
        foreach ([
            ['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['bob']],
            ['actor' => $fixture['users']['admin'], 'tenant' => $fixture['tenants']['alice']],
            ['actor' => WorkbenchStore::id(), 'tenant' => $fixture['tenants']['alice']],
            ['actor' => $fixture['users']['alice'], 'tenant' => WorkbenchStore::id()],
            ['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['alice'], '--permission' => 'hosting.plan'],
            ['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['alice'], '--permission' => '*'],
        ] as $arguments) {
            self::assertSame(1, $command->execute($arguments));
            self::assertFalse($this->commandResult($command)['allowed']);
        }
        $store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $fixture['users']['alice']]);
        self::assertSame(1, $command->execute(['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['alice']]));
        self::assertFalse($this->commandResult($command)['allowed']);
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));
    }

    /** @return iterable<string, array{string}> */
    public static function browserContexts(): iterable
    {
        yield 'user' => ['user'];
        yield 'admin' => ['admin'];
    }

    #[DataProvider('browserContexts')]
    public function testBrowserRuntimeCannotUseOperatorAccessCheckCommand(string $context): void
    {
        [$command, $store, $fixture] = $this->command($context);
        self::assertSame(2, $command->execute(['actor' => $fixture['users']['alice'], 'tenant' => $fixture['tenants']['alice']]));
        self::assertFalse($this->commandResult($command)['allowed']);
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame(0, (int) $store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));
    }
}
