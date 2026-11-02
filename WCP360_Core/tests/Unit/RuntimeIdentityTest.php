<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Platform\RuntimeIdentity;

final class RuntimeIdentityTest extends TestCase
{
    /** @return array{0:array<string,array{uid:int,gid:int,no_login:bool}>,1:array<string,int>} */
    private function directory(): array
    {
        return [[
            'wcp360-user' => ['uid' => 1101, 'gid' => 1201, 'no_login' => true],
            'wcp360-admin' => ['uid' => 1102, 'gid' => 1202, 'no_login' => true],
            'wcp360-worker' => ['uid' => 1103, 'gid' => 1203, 'no_login' => true],
            'wcpd' => ['uid' => 1104, 'gid' => 1204, 'no_login' => true],
            'root' => ['uid' => 0, 'gid' => 0, 'no_login' => false],
        ], ['wcp360-user' => 1201, 'wcp360-admin' => 1202, 'wcp360-worker' => 1203, 'wcpd' => 1204,
            'root' => 0, 'wcp360-control' => 1301, 'wcp360-priv' => 1302]];
    }

    private function procStatus(int $uid = 1101, int $gid = 1201, string $groups = '1201 1301'): string
    {
        return "Name:\tphp\nUid:\t$uid\t$uid\t$uid\t$uid\nGid:\t$gid\t$gid\t$gid\t$gid\nGroups:\t$groups\nCapInh:\t0000000000000000\nCapPrm:\t0000000000000000\nCapEff:\t0000000000000000\nCapBnd:\t000001ffffffffff\nCapAmb:\t0000000000000000\nNoNewPrivs:\t1\nSeccomp:\t2\n";
    }

    /** @return iterable<string,array{string,int,int,string,string,string}> */
    public static function validProfiles(): iterable
    {
        yield 'USER' => ['user', 1101, 1201, '1201 1301', 'wcp360-user', 'wcp360-control'];
        yield 'ADMIN' => ['admin', 1102, 1202, '1301', 'wcp360-admin', 'wcp360-control'];
        yield 'worker' => ['worker', 1103, 1203, '', 'wcp360-worker', ''];
        yield 'daemon' => ['wcpd', 1104, 1204, '1302', 'wcpd', 'wcp360-priv'];
        yield 'broker' => ['wcp-privd', 0, 0, '0', 'root', ''];
    }

    #[DataProvider('validProfiles')]
    public function testExactContractIdentitiesMatchOnlyTheirOwnProfile(string $profile, int $uid, int $gid, string $supplementary, string $account, string $required): void
    {
        [$accounts, $groups] = $this->directory();
        $process = RuntimeIdentity::parseStatus($this->procStatus($uid, $gid, $supplementary));
        self::assertNotNull($process);
        $report = RuntimeIdentity::assess($profile, true, $process, $accounts, $groups);
        self::assertTrue($report['matches_profile']);
        self::assertSame($account, $report['expected']['account']);
        self::assertSame($account, $report['expected']['group']);
        self::assertSame($required === '' ? [] : [$required], $report['expected']['supplementary']);
        foreach (['service_context_verified', 'filesystem_access_verified', 'database_isolation_verified', 'minimal_root_qualified', 'runtime_enforced', 'privileged_effects_allowed', 'qualified_release', 'host_changes'] as $flag) self::assertFalse($report[$flag], $flag);
        foreach (array_diff(RuntimeIdentity::profiles(), [$profile]) as $other) {
            self::assertFalse(RuntimeIdentity::assess($other, true, $process, $accounts, $groups)['matches_profile'], $other);
        }
    }

    public function testSavedAndFilesystemCredentialsCannotRetainRoot(): void
    {
        [$accounts, $groups] = $this->directory();
        foreach (['uids', 'gids'] as $tuple) {
            foreach (range(0, 3) as $index) {
                $process = RuntimeIdentity::parseStatus($this->procStatus());
                self::assertNotNull($process);
                $process[$tuple][$index] = 0;
                $report = RuntimeIdentity::assess('user', true, $process, $accounts, $groups);
                self::assertFalse($report['matches_profile'], $tuple.':'.$index);
                self::assertFalse($report['checks'][$tuple === 'uids' ? 'uid_tuple_matches' : 'gid_tuple_matches']);
            }
        }
    }

    public function testUnexpectedOrMissingGroupsAreRefusedAndPrimaryMembershipIsOptional(): void
    {
        [$accounts, $groups] = $this->directory();
        foreach (['', '1201', '1301 0', '1301 1302', '1301 9999', '1301 1202'] as $membership) {
            $report = RuntimeIdentity::assess('user', true, RuntimeIdentity::parseStatus($this->procStatus(groups: $membership)), $accounts, $groups);
            self::assertFalse($report['matches_profile'], $membership);
            self::assertFalse($report['checks']['supplementary_groups_match']);
        }
        foreach (['1301', '1201 1301', '1301 1201 1301'] as $membership) {
            self::assertTrue(RuntimeIdentity::assess('user', true, RuntimeIdentity::parseStatus($this->procStatus(groups: $membership)), $accounts, $groups)['matches_profile']);
        }
        foreach (['1301', '1302', '0'] as $membership) {
            self::assertFalse(RuntimeIdentity::assess('worker', true, RuntimeIdentity::parseStatus($this->procStatus(1103, 1203, $membership)), $accounts, $groups)['matches_profile']);
        }
    }

    public function testEveryNonRootCapabilitySetAndNoNewPrivilegesAreChecked(): void
    {
        [$accounts, $groups] = $this->directory();
        foreach (['CapInh', 'CapPrm', 'CapEff', 'CapAmb'] as $capability) {
            $content = str_replace($capability.":\t0000000000000000", $capability.":\t0000000000000001", $this->procStatus());
            $report = RuntimeIdentity::assess('user', true, RuntimeIdentity::parseStatus($content), $accounts, $groups);
            self::assertFalse($report['matches_profile'], $capability);
            self::assertFalse($report['checks']['non_root_capabilities_clear']);
        }
        $report = RuntimeIdentity::assess('user', true, RuntimeIdentity::parseStatus(str_replace("NoNewPrivs:\t1", "NoNewPrivs:\t0", $this->procStatus())), $accounts, $groups);
        self::assertFalse($report['matches_profile']);
        self::assertFalse($report['checks']['no_new_privileges']);
    }

    public function testRootIdentityMatchDoesNotQualifyMinimalRootOrPrivilegedEffects(): void
    {
        [$accounts, $groups] = $this->directory();
        $content = str_replace("CapEff:\t0000000000000000", "CapEff:\t00000000a80425fb", $this->procStatus(0, 0, '0'));
        $report = RuntimeIdentity::assess('wcp-privd', true, RuntimeIdentity::parseStatus($content), $accounts, $groups);
        self::assertTrue($report['matches_profile']);
        self::assertNull($report['checks']['non_root_capabilities_clear']);
        self::assertFalse($report['minimal_root_qualified']);
        self::assertFalse($report['privileged_effects_allowed']);
        self::assertFalse($report['service_context_verified']);
    }

    public function testCanonicalAliasesMissingAccountsAndWrongPrimaryGroupsAreRefused(): void
    {
        [$accounts, $groups] = $this->directory();
        $process = RuntimeIdentity::parseStatus($this->procStatus());
        $variants = [];
        $copy = $accounts; $copy['wcp360-admin']['uid'] = 1101; $variants[] = [$copy, $groups];
        $copy = $groups; $copy['wcp360-admin'] = 1201; $variants[] = [$accounts, $copy];
        $copy = $groups; $copy['wcp360-control'] = 1302; $variants[] = [$accounts, $copy];
        $copy = $accounts; unset($copy['wcp360-worker']); $variants[] = [$copy, $groups];
        $copy = $groups; unset($copy['wcp360-priv']); $variants[] = [$accounts, $copy];
        $copy = $accounts; $copy['wcp360-admin']['no_login'] = false; $variants[] = [$copy, $groups];
        $copy = $accounts; $copy['wcp360-user']['gid'] = 9999; $variants[] = [$copy, $groups];
        $copy = $accounts; $copy['wcpd']['uid'] = 0; $variants[] = [$copy, $groups];
        $copy = $accounts; $copy['wcp360-worker']['uid'] = -1; $variants[] = [$copy, $groups];
        $copy = $groups; $copy['wcp360-worker'] = 4294967295; $variants[] = [$accounts, $copy];
        foreach ($variants as $index => [$directory, $mapping]) {
            self::assertFalse(RuntimeIdentity::assess('user', true, $process, $directory, $mapping)['matches_profile'], (string) $index);
        }
    }

    public function testUnknownObservationsCannotPassAndNoUnrelatedAccountDataIsReturned(): void
    {
        [$accounts, $groups] = $this->directory();
        foreach ([[false, null], [true, null], [false, RuntimeIdentity::parseStatus($this->procStatus())]] as [$linux, $process]) {
            self::assertFalse(RuntimeIdentity::assess('user', $linux, $process, $accounts, $groups)['matches_profile']);
        }
        $accounts['foreign'] = ['uid' => 99, 'gid' => 99, 'no_login' => false];
        $accounts['wcp360-user']['passwd'] = 'must-not-be-retained';
        $accounts['root']['home'] = '/private-home-must-not-be-retained';
        $groups['sudo'] = 27;
        $report = RuntimeIdentity::assess('user', true, RuntimeIdentity::parseStatus($this->procStatus()), $accounts, $groups);
        self::assertArrayNotHasKey('foreign', $report['accounts']);
        self::assertArrayNotHasKey('sudo', $report['groups']);
        self::assertSame(['uid', 'gid', 'no_login'], array_keys($report['accounts']['wcp360-user']));
        self::assertStringNotContainsString('must-not-be-retained', json_encode($report, JSON_THROW_ON_ERROR));
        self::assertSame(['user', 'admin', 'worker', 'wcpd', 'wcp-privd'], RuntimeIdentity::profiles());
    }

    /** @return iterable<string,array{string}> */
    public static function invalidStatus(): iterable
    {
        $valid = "Uid:\t1 1 1 1\nGid:\t2 2 2 2\nGroups:\t2 3\nCapInh:\t0000000000000000\nCapPrm:\t0000000000000000\nCapEff:\t0000000000000000\nCapBnd:\t00000000ffffffff\nCapAmb:\t0000000000000000\nNoNewPrivs:\t1\n";
        yield 'empty' => [''];
        yield 'truncated' => [substr($valid, 0, -1)];
        yield 'oversized' => [str_repeat('x', 65536)."\n".$valid];
        yield 'NUL' => [$valid."Name:\tbad\0name\n"];
        yield 'CR' => [str_replace("\n", "\r\n", $valid)];
        foreach (['Uid', 'Gid', 'Groups', 'CapInh', 'CapPrm', 'CapEff', 'CapBnd', 'CapAmb', 'NoNewPrivs'] as $field) {
            yield 'missing '.$field => [(string) preg_replace('/^'.$field.':.*\n/m', '', $valid)];
            yield 'duplicate '.$field => [$valid.(string) preg_replace('/^(?!'.$field.':).*\n/m', '', $valid)];
        }
        yield 'short UID tuple' => [str_replace('1 1 1 1', '1 1 1', $valid)];
        yield 'negative UID' => [str_replace('1 1 1 1', '-1 1 1 1', $valid)];
        yield 'overflow UID' => [str_replace('1 1 1 1', '4294967295 1 1 1', $valid)];
        yield 'long GID tuple' => [str_replace('2 2 2 2', '2 2 2 2 2', $valid)];
        yield 'group overflow' => [str_replace("Groups:\t2 3", "Groups:\t4294967295", $valid)];
        yield 'too many groups' => [str_replace("Groups:\t2 3", "Groups:\t".str_repeat('1 ', 1025), $valid)];
        yield 'bad capability' => [str_replace("CapEff:\t0000000000000000", "CapEff:\t-000000000000001", $valid)];
        yield 'short capability' => [str_replace("CapAmb:\t0000000000000000", "CapAmb:\t0", $valid)];
        yield 'bad no_new_privs' => [str_replace("NoNewPrivs:\t1", "NoNewPrivs:\t2", $valid)];
        yield 'malformed extra credential' => [$valid."Uid : 0 0 0 0\n"];
    }

    #[DataProvider('invalidStatus')]
    public function testMalformedOrIncompleteKernelObservationsStayUnknown(string $content): void
    {
        self::assertNull(RuntimeIdentity::parseStatus($content));
    }

    public function testUnknownProfilesCannotBecomeRootOrSetup(): void
    {
        foreach (['setup', 'root', 'USER', 'admin;id', '../user', ''] as $profile) {
            try {
                RuntimeIdentity::inspect($profile);
                self::fail('Unknown profile accepted.');
            } catch (\InvalidArgumentException $exception) {
                self::assertSame('Unknown runtime identity profile.', $exception->getMessage());
            }
        }
    }
}
