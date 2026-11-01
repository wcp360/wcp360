<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Infrastructure\ArtifactInput;

final class ArtifactInputTest extends TestCase
{
    private string $root;
    /** @var list<string> */
    private array $files = [];
    /** @var list<string> */
    private array $directories = [];

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2).'/var/artifact-input-test-'.bin2hex(random_bytes(8));
        self::assertTrue(mkdir($this->root, 0700));
        $this->directories[] = $this->root;
    }

    protected function tearDown(): void
    {
        // Only the exact fixture entries created by this test are removed.
        foreach (array_reverse($this->files) as $path) {
            if (is_link($path) || @lstat($path) !== false) {
                unlink($path);
            }
        }
        foreach (array_reverse($this->directories) as $path) {
            if (is_dir($path)) {
                chmod($path, 0700);
                rmdir($path);
            }
        }
        parent::tearDown();
    }

    public function testBoundedReadsPreserveExactBinaryBytesAndAcceptTheBoundary(): void
    {
        $bytes = "\x00\xffmanifest\r\n".str_repeat('x', 16372);
        self::assertSame(16384, strlen($bytes));
        $path = $this->file('manifest.bin', $bytes);
        self::assertSame($bytes, ArtifactInput::read($path, 16384));
        self::assertSame($bytes, file_get_contents($path));
        $this->refuses(static fn () => ArtifactInput::read($path, 16383));
        self::assertSame('', ArtifactInput::read($this->file('empty', ''), 1));
    }

    public function testReadCeilingsAreCheckedBeforeOpeningAnyPath(): void
    {
        foreach ([-1, 0, 16385, PHP_INT_MAX] as $ceiling) {
            $this->refuses(fn () => ArtifactInput::read($this->root.'/missing-secret-path', $ceiling));
        }
    }

    public function testHashStreamsMultipleChunksAndRequiresTheExactDeclaredSize(): void
    {
        $bytes = str_repeat("\x00\xffcandidate-payload\n", 16000).'last-byte';
        $path = $this->file('payload.tar.gz', $bytes);
        self::assertGreaterThan(65536 * 4, strlen($bytes));
        self::assertSame(hash('sha256', $bytes), ArtifactInput::sha256($path, strlen($bytes)));
        $this->refuses(static fn () => ArtifactInput::sha256($path, strlen($bytes) - 1));
        $this->refuses(static fn () => ArtifactInput::sha256($path, strlen($bytes) + 1));
        file_put_contents($path, str_repeat('x', strlen($bytes)));
        self::assertNotSame(hash('sha256', $bytes), ArtifactInput::sha256($path, strlen($bytes)));
    }

    public function testPayloadCeilingsAndEmptyPayloadAreRefusedWithoutCreatingAnything(): void
    {
        self::assertSame(2147483648, ArtifactInput::MAX_ARTIFACT_BYTES);
        foreach ([-1, 0, ArtifactInput::MAX_ARTIFACT_BYTES + 1, PHP_INT_MAX] as $size) {
            $this->refuses(fn () => ArtifactInput::sha256($this->root.'/missing-secret-path', $size));
        }
        $this->refuses(fn () => ArtifactInput::sha256($this->file('empty-payload', ''), 1));
        self::assertFileDoesNotExist($this->root.'/missing-secret-path');
    }

    public function testNonlocalAmbiguousAndTraversalPathsAreRefused(): void
    {
        $path = $this->file('untouched', 'preserve-me');
        foreach ([
            '', 'relative.bin', 'file://'.$path, 'php://memory', '//server/share/file', '\\\\server\\share\\file',
            $this->root.'/../untouched', $this->root.'/./untouched', $this->root.'//untouched',
            $path.'/', $path.":stream", $path."\nprivate-value", $path."\0private-value", str_repeat('x', 4097),
            'C:relative.bin', 'C:/folder/../file', 'C:/folder/file:alternate-stream',
        ] as $invalid) {
            $this->refuses(static fn () => ArtifactInput::read($invalid, 64));
            $this->refuses(static fn () => ArtifactInput::sha256($invalid, 11));
        }
        self::assertSame('preserve-me', file_get_contents($path));
    }

    public function testMissingPathsAndDirectoriesAreRefusedWithoutMutations(): void
    {
        $this->refuses(fn () => ArtifactInput::read($this->root.'/missing-private-name', 64));
        $this->refuses(fn () => ArtifactInput::sha256($this->root, 1));
        self::assertSame(['.', '..'], scandir($this->root));
    }

    public function testAnotherOperatingSystemsAbsoluteSyntaxCannotBecomeARelativeOrImplicitDrivePath(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $path = str_replace('\\', '/', $this->file('implicit-drive', 'untouched'));
            self::assertMatchesRegularExpression('~\A[A-Za-z]:/~D', $path);
            $withoutDrive = substr($path, 2);
            $this->refuses(static fn () => ArtifactInput::read($withoutDrive, 64));
            $this->refuses(static fn () => ArtifactInput::sha256($withoutDrive, 9));

            return;
        }

        // Unix can hold a literal C: directory. Treating drive syntax as absolute
        // here would silently select this process-relative payload instead.
        $this->directory('C:');
        $this->file('C:/payload', 'untouched');
        $previous = getcwd();
        self::assertIsString($previous);
        self::assertTrue(chdir($this->root));
        try {
            $this->refuses(static fn () => ArtifactInput::read('C:/payload', 64));
            $this->refuses(static fn () => ArtifactInput::sha256('C:/payload', 9));
        } finally {
            chdir($previous);
        }
    }

    public function testAnExclusiveWriterLockPreventsObservation(): void
    {
        $path = $this->file('locked', 'do-not-read-while-writing');
        $writer = fopen($path, 'r+b');
        self::assertIsResource($writer);
        self::assertTrue(flock($writer, LOCK_EX | LOCK_NB));
        try {
            $this->refuses(static fn () => ArtifactInput::read($path, 64));
            $this->refuses(static fn () => ArtifactInput::sha256($path, strlen('do-not-read-while-writing')));
        } finally {
            flock($writer, LOCK_UN);
            fclose($writer);
        }
        self::assertSame('do-not-read-while-writing', ArtifactInput::read($path, 64));
    }

    public function testSymlinkFilesAndAncestorsAreRejectedWithoutFollowingThem(): void
    {
        $this->linux();
        $path = $this->file('original', 'untouched');
        $alias = $this->root.'/file-alias';
        self::assertTrue(symlink($path, $alias));
        $this->files[] = $alias;
        $this->refuses(static fn () => ArtifactInput::read($alias, 64));
        $directoryAlias = $this->root.'/directory-alias';
        self::assertTrue(symlink($this->root, $directoryAlias));
        $this->files[] = $directoryAlias;
        $this->refuses(static fn () => ArtifactInput::sha256($directoryAlias.'/original', 9));
        self::assertSame('untouched', file_get_contents($path));
    }

    public function testHardLinksAreRejectedForBothReadAndHash(): void
    {
        $this->linux();
        $path = $this->file('original', 'untouched');
        $alias = $this->root.'/hard-link';
        self::assertTrue(link($path, $alias));
        $this->files[] = $alias;
        $this->refuses(static fn () => ArtifactInput::read($path, 64));
        $this->refuses(static fn () => ArtifactInput::sha256($alias, 9));
        self::assertSame('untouched', file_get_contents($path));
    }

    public function testFifoIsRejectedBeforeOpeningIt(): void
    {
        $this->linux();
        self::assertTrue(function_exists('posix_mkfifo'), 'Linux verification requires FIFO fixture support.');
        $fifo = $this->root.'/fifo';
        self::assertTrue(posix_mkfifo($fifo, 0600));
        $this->files[] = $fifo;
        $this->refuses(static fn () => ArtifactInput::read($fifo, 64));
        $this->refuses(static fn () => ArtifactInput::sha256($fifo, 1));
    }

    public function testWritableFilesAndImmediateParentsAreRejectedEvenWhenSticky(): void
    {
        $this->linux();
        $path = $this->file('payload', 'untouched');
        foreach ([0620, 0602] as $mode) {
            self::assertTrue(chmod($path, $mode));
            $this->refuses(static fn () => ArtifactInput::read($path, 64));
            $this->refuses(static fn () => ArtifactInput::sha256($path, 9));
        }
        self::assertTrue(chmod($path, 0644));
        self::assertSame('untouched', ArtifactInput::read($path, 64));
        foreach ([0777, 01777] as $mode) {
            self::assertTrue(chmod($this->root, $mode));
            $this->refuses(static fn () => ArtifactInput::read($path, 64));
        }
        self::assertTrue(chmod($this->root, 0700));
    }

    public function testSharedStickyAncestorIsAllowedOnlyAboveAProtectedParent(): void
    {
        $this->linux();
        $shared = $this->directory('shared');
        $protected = $this->directory('shared/protected');
        $path = $this->file('shared/protected/payload', 'untouched');
        self::assertTrue(chmod($shared, 01777));
        self::assertSame('untouched', ArtifactInput::read($path, 64));
        self::assertSame(hash('sha256', 'untouched'), ArtifactInput::sha256($path, 9));
        self::assertTrue(chmod($shared, 0777));
        $this->refuses(static fn () => ArtifactInput::read($path, 64));
        self::assertSame(0700, fileperms($protected) & 0777);
    }

    private function file(string $relative, string $bytes): string
    {
        $path = $this->root.'/'.$relative;
        self::assertSame(strlen($bytes), file_put_contents($path, $bytes));
        self::assertTrue(chmod($path, 0600));
        $this->files[] = $path;

        return $path;
    }

    private function directory(string $relative): string
    {
        $path = $this->root.'/'.$relative;
        self::assertTrue(mkdir($path, 0700));
        $this->directories[] = $path;

        return $path;
    }

    private function refuses(callable $action): void
    {
        try {
            $action();
            self::fail('Unsafe artifact input was accepted.');
        } catch (\RuntimeException $error) {
            self::assertSame('Candidate artifact input is unavailable or unsafe.', $error->getMessage());
        }
    }

    private function linux(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('POSIX links, FIFO and permission enforcement are exercised on Linux targets.');
        }
        self::assertSame('Linux', PHP_OS_FAMILY);
    }
}
