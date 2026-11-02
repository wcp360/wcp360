<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Infrastructure\InstallationJournal;

final class InstallationJournalTest extends TestCase
{
    private string $root;
    private InstallationJournal $journal;

    protected function setUp(): void
    {
        $base = dirname(__DIR__, 2).'/var/journal-tests';
        if (!is_dir($base)) {
            mkdir($base, 0700, true);
        }
        $this->root = $base.'/'.bin2hex(random_bytes(10));
        mkdir($this->root, 0700);
        $this->journal = new InstallationJournal($this->root);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->root);
    }

    public function testAbsentStatusAndMissingResumeDoNotCreateAnything(): void
    {
        $report = $this->journal->inspect();
        self::assertSame('absent', $report['status']);
        self::assertFalse($report['qualified_release']);
        self::assertDirectoryDoesNotExist($this->root.'/var');
        $called = false;
        $this->refuses(function () use (&$called): void {
            $this->journal->execute(function () use (&$called): string {
                $called = true;
                return $this->binding();
            }, true, static fn (string $step): int => 0);
        }, 'no installation journal');
        self::assertFalse($called);
        self::assertDirectoryDoesNotExist($this->root.'/var');
    }

    public function testFreshExecutionUsesOnlyFixedStepsAndPersistsPrivateCheckpoints(): void
    {
        $seen = [];
        $report = $this->journal->execute($this->binding(), false, function (string $step) use (&$seen): int {
            $seen[] = $step;
            $progress = $this->journal->inspect();
            self::assertSame('uncertain', $progress['status']);
            self::assertSame($step, $progress['active_step']);
            return 0;
        });
        self::assertSame(InstallationJournal::STEPS, $seen);
        self::assertSame('complete', $report['status']);
        self::assertFalse($report['requires_manual_reconciliation']);
        self::assertFalse($report['qualified_release']);
        self::assertSame(array_fill(0, 6, 'SUCCEEDED'), array_column($report['steps'], 'status'));
        self::assertSame($report, $this->journal->inspect());
        self::assertSame([], glob($this->root.'/var/installer/.candidate-install-*.tmp'));
        if (PHP_OS_FAMILY !== 'Windows') {
            self::assertSame(0700, fileperms($this->root.'/var/installer') & 0777);
            self::assertSame(0600, fileperms($this->statePath()) & 0777);
            self::assertSame(0600, fileperms($this->root.'/var/installer/.lock') & 0777);
        }
        self::assertArrayNotHasKey('binding', $report);
        self::assertStringNotContainsString($this->binding(), json_encode($report, JSON_THROW_ON_ERROR));
    }

    public function testExistingJournalRefusesFreshRunBeforeBindingPreparation(): void
    {
        $this->complete();
        $before = file_get_contents($this->statePath());
        $prepared = false;
        $this->refuses(function () use (&$prepared): void {
            $this->journal->execute(function () use (&$prepared): string {
                $prepared = true;
                return $this->binding();
            }, false, static fn (string $step): int => 0);
        }, '--resume');
        self::assertFalse($prepared);
        self::assertSame($before, file_get_contents($this->statePath()));
    }

    public function testStatusIsReadOnlyAndHidesPrivateBinding(): void
    {
        $this->complete();
        $before = file_get_contents($this->statePath());
        $entries = scandir($this->root.'/var/installer');
        self::assertSame('complete', $this->journal->inspect()['status']);
        self::assertSame($before, file_get_contents($this->statePath()));
        self::assertSame($entries, scandir($this->root.'/var/installer'));
    }

    /** @return iterable<string, array{string}> */
    public static function safeSteps(): iterable
    {
        foreach (array_slice(InstallationJournal::STEPS, 0, 5) as $step) {
            yield $step => [$step];
        }
    }

    #[DataProvider('safeSteps')]
    public function testExplicitResumeRegeneratesAllSafeOutputsAfterKnownFailure(string $failed): void
    {
        $first = $this->journal->execute($this->binding(), false, static fn (string $step): int => $step === $failed ? 17 : 0);
        self::assertSame('failed', $first['status']);
        self::assertSame($failed, $first['active_step']);
        $seen = [];
        $report = $this->journal->execute($this->binding(), true, static function (string $step) use (&$seen): int {
            $seen[] = $step;
            return 0;
        });
        self::assertSame(InstallationJournal::STEPS, $seen);
        self::assertSame('complete', $report['status']);
    }

    public function testInterruptedSafeStepCanResumeAndRegenerateEarlierOutputs(): void
    {
        $this->journal->execute($this->binding(), false, static fn (string $step): int => $step === 'planner_build' ? 19 : 0);
        $state = $this->state();
        $state['steps'][2]['status'] = 'RUNNING';
        $state['steps'][2]['exit_code'] = null;
        $this->replaceState($state);
        self::assertSame('uncertain', $this->journal->inspect()['status']);
        self::assertFalse($this->journal->inspect()['requires_manual_reconciliation']);
        $seen = [];
        self::assertSame('complete', $this->journal->execute($this->binding(), true, static function (string $step) use (&$seen): int {
            $seen[] = $step;
            return 0;
        })['status']);
        self::assertSame(InstallationJournal::STEPS, $seen);
    }

    public function testCompletedResumePreservesMigrationAndCallsOnlyReadOnlyVerification(): void
    {
        $this->complete();
        $seen = [];
        $result = $this->journal->execute($this->binding(), true, function (string $step) use (&$seen): int {
            $seen[] = $step;
            self::assertSame('SUCCEEDED', $this->state()['steps'][5]['status']);
            return 0;
        });
        self::assertSame([...array_slice(InstallationJournal::STEPS, 0, 5), 'database_verify'], $seen);
        self::assertSame('complete', $result['status']);
        self::assertSame(['status' => 'SUCCEEDED', 'exit_code' => 0], $result['verification']);
        self::assertCount(6, $result['steps']);
    }

    public function testVerificationFailureAndInterruptionNeverReplayMigrations(): void
    {
        $this->complete();
        $report = $this->journal->execute($this->binding(), true, static fn (string $step): int => $step === 'database_verify' ? 23 : 0);
        self::assertSame('failed', $report['status']);
        self::assertSame('database_verify', $report['active_step']);
        self::assertFalse($report['requires_manual_reconciliation']);
        $state = $this->state();
        $state['verification'] = ['status' => 'RUNNING', 'exit_code' => null];
        $this->replaceState($state);
        self::assertSame('uncertain', $this->journal->inspect()['status']);
        $seen = [];
        self::assertSame('complete', $this->journal->execute($this->binding(), true, static function (string $step) use (&$seen): int {
            $seen[] = $step;
            return 0;
        })['status']);
        self::assertNotContains('database_migrations', $seen);
        self::assertContains('database_verify', $seen);
    }

    public function testFailedMigrationRefusesEveryCallbackOnResume(): void
    {
        $result = $this->journal->execute($this->binding(), false, static fn (string $step): int => $step === 'database_migrations' ? 31 : 0);
        self::assertTrue($result['requires_manual_reconciliation']);
        self::assertSame('failed', $result['status']);
        $this->assertMigrationRefusesResume();
    }

    public function testInterruptedMigrationRefusesEveryCallbackOnResume(): void
    {
        $this->complete();
        $state = $this->state();
        $state['steps'][5] = ['id' => 'database_migrations', 'status' => 'RUNNING', 'exit_code' => null];
        $this->replaceState($state);
        self::assertSame('uncertain', $this->journal->inspect()['status']);
        self::assertTrue($this->journal->inspect()['requires_manual_reconciliation']);
        $this->assertMigrationRefusesResume();
    }

    /** @return iterable<string, array{string}> */
    public static function interruptedSteps(): iterable
    {
        yield 'safe-planner' => ['planner_build'];
        yield 'irreversible-migration' => ['database_migrations'];
    }

    #[DataProvider('interruptedSteps')]
    public function testActualProcessExitLeavesDurableUncertainCheckpoint(string $interrupted): void
    {
        $script = $this->root.'/interrupted.php';
        $source = "<?php\ndeclare(strict_types=1);\nrequire ".var_export(dirname(__DIR__, 2).'/src/Module/Setup/Infrastructure/InstallationJournal.php', true).";\n";
        $source .= '$journal = new \\Wcp360\\Core\\Module\\Setup\\Infrastructure\\InstallationJournal('.var_export($this->root, true).");\n";
        $source .= '$journal->execute('.var_export($this->binding(), true).', false, static function (string $step): int { if ($step === '.var_export($interrupted, true).") { exit(91); } return 0; });\n";
        file_put_contents($script, $source);
        $process = proc_open([PHP_BINARY, $script], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $this->root, null, ['bypass_shell' => true]);
        self::assertIsResource($process);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        self::assertSame(91, proc_close($process), 'Child installer must exit inside its active callback.');
        self::assertSame('', $stdout);
        self::assertSame('', $stderr);
        $report = $this->journal->inspect();
        self::assertSame('uncertain', $report['status']);
        self::assertSame($interrupted, $report['active_step']);
        if ($interrupted === 'database_migrations') {
            $this->assertMigrationRefusesResume();
        } else {
            self::assertSame('complete', $this->journal->execute($this->binding(), true, static fn (string $step): int => 0)['status']);
        }
    }

    public function testChangedInputsRefuseResumeBeforeAnyStepAndPreserveJournal(): void
    {
        $this->complete();
        $before = file_get_contents($this->statePath());
        $called = false;
        $this->refuses(function () use (&$called): void {
            $this->journal->execute(str_repeat('b', 64), true, static function (string $step) use (&$called): int {
                $called = true;
                return 0;
            });
        }, 'inputs changed');
        self::assertFalse($called);
        self::assertSame($before, file_get_contents($this->statePath()));
    }

    public function testLockIsHeldAcrossBindingPreparationAndEveryCallback(): void
    {
        $assertLocked = function (): void {
            $second = new InstallationJournal($this->root);
            $this->refuses(static fn () => $second->execute(str_repeat('a', 64), false, static fn (string $step): int => 0), 'holds the installer lock');
        };
        $result = $this->journal->execute(function () use ($assertLocked): string {
            $assertLocked();
            return $this->binding();
        }, false, static function (string $step) use ($assertLocked): int {
            $assertLocked();
            return 0;
        });
        self::assertSame('complete', $result['status']);
    }

    public function testExistingExternalLockRefusesBeforeBindingPreparation(): void
    {
        $this->complete();
        $handle = fopen($this->root.'/var/installer/.lock', 'r+b');
        self::assertIsResource($handle);
        self::assertTrue(flock($handle, LOCK_EX | LOCK_NB));
        $prepared = false;
        try {
            $this->refuses(function () use (&$prepared): void {
                $this->journal->execute(function () use (&$prepared): string {
                    $prepared = true;
                    return $this->binding();
                }, true, static fn (string $step): int => 0);
            }, 'holds the installer lock');
            self::assertFalse($prepared);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function testCallbackExceptionPersistsGenericFailureWithoutSecretText(): void
    {
        $report = $this->journal->execute($this->binding(), false, static function (string $step): int {
            throw new \RuntimeException('postgresql://private:top-secret@example.invalid/db APP_SECRET=hidden');
        });
        self::assertSame('failed', $report['status']);
        self::assertSame('composer_install', $report['active_step']);
        self::assertSame(1, $report['steps'][0]['exit_code']);
        $serialized = file_get_contents($this->statePath()).json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('top-secret', $serialized);
        self::assertStringNotContainsString('APP_SECRET', $serialized);
        self::assertStringNotContainsString('postgresql', $serialized);
    }

    public function testFingerprintPreparationExceptionCreatesNoJournalAndDoesNotLeakDetails(): void
    {
        $this->refuses(function (): void {
            $this->journal->execute(static function (): string {
                throw new \RuntimeException('must-never-be-exposed');
            }, false, static fn (string $step): int => 0);
        }, 'fingerprint could not be prepared');
        self::assertFileDoesNotExist($this->statePath());
    }

    public function testInvalidFingerprintRefusesWithoutFilesystemChanges(): void
    {
        $this->refuses(fn () => $this->journal->execute(str_repeat('A', 64), false, static fn (string $step): int => 0), 'fingerprint is invalid');
        self::assertDirectoryDoesNotExist($this->root.'/var');
    }

    /** @return iterable<string, array{string}> */
    public static function invalidJournals(): iterable
    {
        yield 'malformed' => ['{'];
        yield 'too-deep' => [str_repeat('[', 18).str_repeat(']', 18)];
        yield 'wrong-container' => ['[]'];
        yield 'oversized' => [str_repeat(' ', 32769)];
    }

    #[DataProvider('invalidJournals')]
    public function testMalformedAndOversizedJournalRefusesBeforeCallbacks(string $bytes): void
    {
        $this->complete();
        file_put_contents($this->statePath(), $bytes);
        $called = false;
        $this->refuses(function () use (&$called): void {
            $this->journal->execute(function () use (&$called): string {
                $called = true;
                return $this->binding();
            }, true, static fn (string $step): int => 0);
        }, 'journal');
        self::assertFalse($called);
        self::assertSame($bytes, file_get_contents($this->statePath()));
    }

    public function testUnknownFieldsStepsTypesAndOrderingAreRejected(): void
    {
        $this->complete();
        $original = $this->state();
        $changes = [
            static function (array $state): array { $state['command'] = 'arbitrary'; return $state; },
            static function (array $state): array { $state['schema'] = 'unreviewed/2'; return $state; },
            static function (array $state): array { $state['binding'] = strtoupper($state['binding']); return $state; },
            static function (array $state): array { $state['steps'][0]['id'] = 'unknown'; return $state; },
            static function (array $state): array { $state['steps'][0]['stdout'] = 'secret'; return $state; },
            static function (array $state): array { $state['steps'][0]['status'] = 'SKIPPED'; return $state; },
            static function (array $state): array { $state['steps'][0]['exit_code'] = '0'; return $state; },
            static function (array $state): array { $state['steps'][0]['status'] = 'PENDING'; $state['steps'][0]['exit_code'] = null; return $state; },
            static function (array $state): array { $state['steps'][1]['status'] = 'FAILED'; $state['steps'][1]['exit_code'] = 0; return $state; },
            static function (array $state): array { $state['steps'] = array_reverse($state['steps']); return $state; },
            static function (array $state): array { unset($state['steps'][0]); return $state; },
            static function (array $state): array { $state['verification'] = ['status' => 'SUCCEEDED', 'exit_code' => null]; return $state; },
            static function (array $state): array { $state['verification']['status'] = 'UNKNOWN'; return $state; },
        ];
        foreach ($changes as $change) {
            $this->replaceState($change($original));
            $this->refuses(fn () => $this->journal->inspect(), 'schema or step order');
        }
    }

    /** @return iterable<string, array{string}> */
    public static function linkTargets(): iterable
    {
        yield 'var' => ['var'];
        yield 'installer-directory' => ['installer'];
        yield 'state' => ['state'];
        yield 'lock' => ['lock'];
        yield 'ancestor' => ['ancestor'];
    }

    #[DataProvider('linkTargets')]
    public function testSymlinkPathsAreRefusedWithoutFollowingThem(string $target): void
    {
        $this->requirePosix();
        $outside = $this->root.'/outside';
        mkdir($outside, 0700);
        file_put_contents($outside.'/untouched', 'unchanged');
        if ($target === 'ancestor') {
            self::assertTrue(symlink($this->root, $this->root.'/alias'));
            $this->refuses(fn () => new InstallationJournal($this->root.'/alias'), 'ancestors');
        } elseif ($target === 'var') {
            self::assertTrue(symlink($outside, $this->root.'/var'));
            $this->refuses(fn () => $this->journal->inspect(), 'real directories');
        } else {
            mkdir($this->root.'/var', 0700);
            if ($target === 'installer') {
                self::assertTrue(symlink($outside, $this->root.'/var/installer'));
                $this->refuses(fn () => $this->journal->inspect(), 'real directories');
            } else {
                mkdir($this->root.'/var/installer', 0700);
                $link = $target === 'state' ? $this->statePath() : $this->root.'/var/installer/.lock';
                self::assertTrue(symlink($outside.'/untouched', $link));
                $this->refuses(fn () => $this->journal->inspect(), 'regular files');
            }
        }
        self::assertSame('unchanged', file_get_contents($outside.'/untouched'));
        self::assertSame(['.', '..', 'untouched'], scandir($outside));
    }

    public function testDirectoryAndFifoCannotStandInForJournalFiles(): void
    {
        $this->complete();
        unlink($this->statePath());
        mkdir($this->statePath(), 0700);
        $this->refuses(fn () => $this->journal->inspect(), 'regular files');
        rmdir($this->statePath());
        if (PHP_OS_FAMILY !== 'Windows' && function_exists('posix_mkfifo')) {
            self::assertTrue(posix_mkfifo($this->statePath(), 0600));
            $this->refuses(fn () => $this->journal->inspect(), 'regular files');
        }
    }

    public function testMultipleHardlinksAreRefusedForStateAndLock(): void
    {
        $this->requirePosix();
        $this->complete();
        foreach ([$this->statePath(), $this->root.'/var/installer/.lock'] as $file) {
            self::assertTrue(link($file, $this->root.'/second-link'));
            $this->refuses(fn () => $this->journal->inspect(), 'regular files');
            unlink($this->root.'/second-link');
        }
    }

    public function testPrivatePermissionsAreEnforcedButOwnedSharedVarIsSupported(): void
    {
        $this->requirePosix();
        mkdir($this->root.'/var', 01777);
        chmod($this->root.'/var', 01777);
        $this->complete();
        foreach ([$this->statePath(), $this->root.'/var/installer/.lock'] as $file) {
            chmod($file, 0640);
            $this->refuses(fn () => $this->journal->inspect(), 'private to their owner');
            chmod($file, 0600);
        }
        chmod($this->root.'/var/installer', 0755);
        $this->refuses(fn () => $this->journal->inspect(), 'private to its owner');
        chmod($this->root.'/var/installer', 0700);
    }

    public function testUnsafeWritableCheckoutAndNonStickyVarAreRefused(): void
    {
        $this->requirePosix();
        chmod($this->root, 0777);
        try {
            $this->refuses(fn () => new InstallationJournal($this->root), 'checkout must not be writable');
        } finally {
            chmod($this->root, 0700);
        }
        mkdir($this->root.'/var', 0777);
        chmod($this->root.'/var', 0777);
        $this->refuses(fn () => $this->journal->inspect(), 'sticky bit');
        $this->refuses(fn () => $this->journal->execute($this->binding(), false, static fn (string $step): int => 0), 'sticky bit');
        self::assertDirectoryDoesNotExist($this->root.'/var/installer');
    }

    public function testCheckoutOwnedByAnotherIdentityIsRefused(): void
    {
        $this->requirePosix();
        if (!function_exists('posix_geteuid') || posix_geteuid() === 0 || !is_dir('/proc')) {
            self::markTestSkipped('Requires a non-root Linux operator and the root-owned proc mount.');
        }
        $this->refuses(static fn () => new InstallationJournal('/proc'), 'executing operator');
    }

    private function assertMigrationRefusesResume(): void
    {
        $before = file_get_contents($this->statePath());
        $called = false;
        $this->refuses(function () use (&$called): void {
            $this->journal->execute(function () use (&$called): string {
                $called = true;
                return $this->binding();
            }, true, static function (string $step) use (&$called): int {
                $called = true;
                return 0;
            });
        }, 'manual reconciliation');
        self::assertFalse($called);
        self::assertSame($before, file_get_contents($this->statePath()));
    }

    private function complete(): void
    {
        self::assertSame('complete', $this->journal->execute($this->binding(), false, static fn (string $step): int => 0)['status']);
    }

    private function binding(): string
    {
        return str_repeat('a', 64);
    }

    private function statePath(): string
    {
        return $this->root.'/var/installer/candidate-install.json';
    }

    /** @return array<string, mixed> */
    private function state(): array
    {
        return json_decode((string) file_get_contents($this->statePath()), true, 16, JSON_THROW_ON_ERROR);
    }

    /** @param array<string, mixed> $state */
    private function replaceState(array $state): void
    {
        file_put_contents($this->statePath(), json_encode($state, JSON_THROW_ON_ERROR));
    }

    private function refuses(callable $action, string $message): void
    {
        try {
            $action();
            self::fail('An unsafe installer operation was accepted.');
        } catch (\RuntimeException $error) {
            self::assertStringContainsString($message, $error->getMessage());
            self::assertStringNotContainsString('must-never-be-exposed', $error->getMessage());
        }
    }

    private function requirePosix(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('POSIX link, ownership and permission invariants run in each Linux Core job.');
        }
    }

    private function removeDirectory(string $directory): void
    {
        if (is_link($directory) || !is_dir($directory)) {
            unlink($directory);
            return;
        }
        foreach (new \FilesystemIterator($directory) as $entry) {
            $this->removeDirectory($entry->getPathname());
        }
        rmdir($directory);
    }
}
