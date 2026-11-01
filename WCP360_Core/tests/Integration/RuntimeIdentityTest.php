<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\RuntimeIdentity;

final class RuntimeIdentityTest extends TestCase
{
    /** @param list<string> $arguments */
    private function command(array $arguments): Process
    {
        $process = new Process([PHP_BINARY, 'installer/preflight/runtime-identity.php', ...$arguments], dirname(__DIR__, 2), [
            'APP_ENV' => 'prod', 'WCP_CANDIDATE' => '0', 'WCP_CONTEXT' => 'admin',
            'APP_SECRET' => 'identity-test-secret-must-never-appear',
            'DATABASE_URL' => 'postgresql://private:identity-test-password-must-never-appear@invalid.example.test/private',
            'WCP_EXPECTED_USER' => 'root', 'WCP_EXPECTED_UID' => '0',
        ]);
        $process->setTimeout(15);
        $process->run();

        return $process;
    }

    public function testStandaloneProfilesIgnoreApplicationBootstrapAndEnvironmentOverrides(): void
    {
        foreach (['user' => 'wcp360-user', 'admin' => 'wcp360-admin', 'worker' => 'wcp360-worker', 'wcpd' => 'wcpd', 'wcp-privd' => 'root'] as $profile => $account) {
            $process = $this->command([$profile]);
            self::assertSame('', $process->getErrorOutput());
            $report = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
            self::assertSame('candidate-runtime-identity-inspection', $report['mode']);
            self::assertSame('current_process_namespace', $report['scope']);
            self::assertSame($profile, $report['profile']);
            self::assertSame($account, $report['expected']['account']);
            self::assertSame($report['matches_profile'] ? 0 : 1, $process->getExitCode());
            self::assertSame(PHP_OS_FAMILY === 'Linux', $report['checks']['linux']);
            foreach (['service_context_verified', 'filesystem_access_verified', 'database_isolation_verified', 'minimal_root_qualified', 'runtime_enforced', 'privileged_effects_allowed', 'qualified_release', 'host_changes'] as $flag) self::assertFalse($report[$flag], $flag);
            self::assertStringNotContainsString('identity-test-secret', $process->getOutput());
            self::assertStringNotContainsString('identity-test-password', $process->getOutput());
            self::assertStringNotContainsString('invalid.example.test', $process->getOutput());
            foreach ($report['accounts'] as $entry) {
                if ($entry !== null) self::assertSame(['uid', 'gid', 'no_login'], array_keys($entry));
            }
            if (PHP_OS_FAMILY !== 'Linux') {
                self::assertNull($report['observed']);
                self::assertFalse($report['matches_profile']);
                self::assertSame(array_fill(0, 5, null), array_values($report['accounts']));
            }
        }
    }

    /** @return iterable<string,array{list<string>}> */
    public static function invalidArguments(): iterable
    {
        yield 'missing profile' => [[]];
        yield 'lifecycle root is not runtime' => [['setup']];
        yield 'unknown account' => [['root']];
        yield 'apply is forbidden' => [['--apply']];
        yield 'arbitrary UID override' => [['user', '--uid=0']];
        yield 'duplicate profile' => [['user', 'user']];
        yield 'sensitive arbitrary input' => [['postgresql://private:must-not-be-echoed@private.example.test/db']];
    }

    #[DataProvider('invalidArguments')]
    public function testArgumentsCannotChangeProfilesOrTriggerHostEffects(array $arguments): void
    {
        $process = $this->command($arguments);
        self::assertSame(64, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Usage: php installer/preflight/runtime-identity.php user|admin|worker|wcpd|wcp-privd\n", $process->getErrorOutput());
    }

    public function testActualLinuxCredentialsAreObservedAndOnlyCanonicalNssFieldsAreExposed(): void
    {
        $this->requireLinux();
        $report = RuntimeIdentity::inspect('user');
        self::assertNotNull($report['observed'], 'Current thread procfs credentials must be readable in the Linux CI profile.');
        self::assertTrue($report['checks']['process_observed']);
        self::assertCount(4, $report['observed']['uids']);
        self::assertCount(4, $report['observed']['gids']);
        if (function_exists('posix_getuid') && function_exists('posix_geteuid') && function_exists('posix_getgid') && function_exists('posix_getegid')) {
            self::assertSame(posix_getuid(), $report['observed']['uids'][0]);
            self::assertSame(posix_geteuid(), $report['observed']['uids'][1]);
            self::assertSame(posix_getgid(), $report['observed']['gids'][0]);
            self::assertSame(posix_getegid(), $report['observed']['gids'][1]);
        }
        if (function_exists('posix_getgroups')) {
            $groups = posix_getgroups();
            self::assertIsArray($groups);
            $groups = array_values(array_unique($groups));
            sort($groups, SORT_NUMERIC);
            self::assertSame($groups, $report['observed']['groups']);
        }
        foreach ($report['accounts'] as $name => $account) {
            if (function_exists('posix_getpwnam')) {
                $native = posix_getpwnam($name);
                if ($native === false) self::assertNull($account);
                else {
                    self::assertNotNull($account);
                    self::assertSame($native['uid'], $account['uid']);
                    self::assertSame($native['gid'], $account['gid']);
                    self::assertSame(['uid', 'gid', 'no_login'], array_keys($account));
                }
                unset($native);
            } else self::assertNull($account);
        }
        self::assertFalse($report['runtime_enforced']);
        self::assertFalse($report['host_changes']);
    }

    public function testLinuxStandaloneObservationMatchesTheCallingIdentityWithoutRuntimeConfiguration(): void
    {
        $this->requireLinux();
        $direct = RuntimeIdentity::inspect('worker');
        $process = $this->command(['worker']);
        self::assertSame('', $process->getErrorOutput());
        $child = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertNotNull($direct['observed']);
        self::assertNotNull($child['observed']);
        foreach (['uids', 'gids', 'groups', 'no_new_privileges'] as $field) self::assertSame($direct['observed'][$field], $child['observed'][$field], $field);
        self::assertSame($direct['accounts'], $child['accounts']);
        self::assertSame($direct['groups'], $child['groups']);
        self::assertFalse($child['privileged_effects_allowed']);
        self::assertFalse($child['database_isolation_verified']);
    }

    private function requireLinux(): void
    {
        if (PHP_OS_FAMILY !== 'Linux') self::markTestSkipped('Requires actual Linux procfs and the current NSS namespace in CI.');
    }
}
