<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Standalone checkout/configuration binding; never serializes configuration values. */
final class InstallationInputs
{
    private readonly string $root;
    private const TREES = ['src', 'config', 'migrations', 'installer', 'templates', 'bin', 'agents/wcpd/candidate/planner', 'assets'];
    private const FILES = ['composer.json', 'composer.lock', 'importmap.php', 'public/index.php', '.env.example'];
    private const MAX_FILE_BYTES = 4_194_304;

    public function __construct(string $coreRoot)
    {
        $root = realpath($coreRoot);
        if ($root === false || is_link($coreRoot) || !is_dir($root)) {
            throw new \RuntimeException('A real Core checkout directory is required.');
        }
        $this->root = str_replace('\\', '/', $root);
    }

    /** Bootstrap once under the installation lock; an existing file is never overwritten. */
    public function ensureEnvironment(bool $mayCreate): void
    {
        $path = $this->root.'/.env';
        clearstatcache(true, $path);
        if (@lstat($path) !== false) {
            $this->read('.env', true);
            return;
        }
        if (!$mayCreate) {
            throw new \RuntimeException('Installation configuration is missing; resume cannot recreate it.');
        }
        $template = $this->read('.env.example');
        if (substr_count($template, 'replace-with-a-random-local-secret') !== 1) {
            throw new \RuntimeException('The local configuration template has no unique secret placeholder.');
        }
        $mfaKeys = preg_match_all('/^([ \t]*(?:export[ \t]+)?WCP_MFA_KEY[ \t]*=)([^\r\n]*)/m', $template, $mfaMatches, PREG_OFFSET_CAPTURE);
        if ($mfaKeys === false || $mfaKeys > 1 || ($mfaKeys === 1 && trim($mfaMatches[2][0][0]) !== '')) {
            throw new \RuntimeException('The local configuration template must contain at most one empty MFA key assignment.');
        }
        if ($mfaKeys === 1) {
            // Older checkout templates may omit MFA. Never copy a shared key from
            // a template or derive this encryption key from the application secret.
            [$value, $offset] = $mfaMatches[2][0];
            $template = substr_replace($template, bin2hex(random_bytes(32)), $offset, strlen($value));
        }
        $content = str_replace('replace-with-a-random-local-secret', bin2hex(random_bytes(32)), $template);
        $temporary = $this->root.'/.env.bootstrap-'.bin2hex(random_bytes(16));
        $previous = umask(0077);
        $stream = @fopen($temporary, 'xb');
        umask($previous);
        if ($stream === false) {
            throw new \RuntimeException('Cannot prepare private local configuration.');
        }
        try {
            if (!chmod($temporary, 0600) || fwrite($stream, $content) !== strlen($content) || !fflush($stream) || !fsync($stream)) {
                throw new \RuntimeException('Cannot persist private local configuration.');
            }
            fclose($stream);
            $stream = null;
            // Hard-link publication is atomic and, unlike rename(), cannot replace an
            // operator-created destination. The temporary name is removed immediately.
            if (!@link($temporary, $path)) {
                throw new \RuntimeException('Local configuration appeared concurrently or could not be published.');
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
            @unlink($temporary);
        }
        $this->read('.env', true);
    }

    /**
     * Bind every inherited environment value, including caller/terminal metadata.
     * This covers arbitrary ${VARIABLE} references in operator-owned dotenv files.
     * @param array<string, string> $environment
     * @param array<string, string> $runtime Trusted target/tool identity, not commands.
     */
    public function fingerprint(array $environment, array $runtime): string
    {
        $files = [];
        foreach (self::FILES as $file) {
            $files[$file] = hash('sha256', $this->read($file));
        }
        foreach (self::TREES as $tree) {
            $this->collect($tree, $files);
        }
        $entries = scandir($this->root);
        if ($entries === false) {
            throw new \RuntimeException('Cannot inspect checkout configuration.');
        }
        foreach ($entries as $entry) {
            if ($entry === '.env' || str_starts_with($entry, '.env.')) {
                // Temporary bootstrap files are not loadable Dotenv inputs.
                if (str_starts_with($entry, '.env.bootstrap-') || $entry === '.env.example') {
                    continue;
                }
                $files[$entry] = hash('sha256', $this->read($entry, true));
            }
        }
        if (!isset($files['.env'])) {
            throw new \RuntimeException('Installation configuration is missing.');
        }
        ksort($files);
        ksort($environment);
        ksort($runtime);
        return hash('sha256', json_encode(['schema' => 1, 'root' => $this->root, 'files' => $files, 'environment' => $environment, 'runtime' => $runtime], JSON_THROW_ON_ERROR));
    }

    /** @param array<string, string> $files */
    private function collect(string $relative, array &$files): void
    {
        if ($relative === 'assets/vendor') {
            return;
        }
        $path = $this->root.'/'.$relative;
        $stat = @lstat($path);
        if ($stat === false || ($stat['mode'] & 0170000) !== 0040000 || is_link($path)) {
            throw new \RuntimeException('Installer source directories must be real checkout directories.');
        }
        $entries = scandir($path);
        if ($entries === false) {
            throw new \RuntimeException('Cannot inspect installer sources.');
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $child = $relative.'/'.$entry;
            // Symfony generates this IDE/type reference during debug container
            // compilation. This application's Kernel never loads it.
            if ($child === 'config/reference.php') {
                continue;
            }
            if (count($files) >= 10000) {
                throw new \RuntimeException('Installer source inventory exceeds its limit.');
            }
            if (is_dir($this->root.'/'.$child) && !is_link($this->root.'/'.$child)) {
                $this->collect($child, $files);
            } else {
                $files[$child] = hash('sha256', $this->read($child));
            }
        }
    }

    private function read(string $relative, bool $private = false): string
    {
        $path = $this->root.'/'.$relative;
        $stat = @lstat($path);
        if ($stat === false || ($stat['mode'] & 0170000) !== 0100000 || $stat['nlink'] !== 1 || is_link($path) || $stat['size'] > self::MAX_FILE_BYTES) {
            throw new \RuntimeException('Installer inputs must be bounded regular files without links.');
        }
        if ($private && PHP_OS_FAMILY !== 'Windows' && (($stat['mode'] & 0077) !== 0 || !function_exists('posix_geteuid') || $stat['uid'] !== posix_geteuid())) {
            throw new \RuntimeException('Local installation configuration must be private and owned by the operator (chmod 600).');
        }
        $stream = @fopen($path, 'rb');
        if ($stream === false) {
            throw new \RuntimeException('Cannot read installer input.');
        }
        try {
            $opened = fstat($stream);
            $content = stream_get_contents($stream, self::MAX_FILE_BYTES + 1);
            if ($opened === false || $opened['dev'] !== $stat['dev'] || $opened['ino'] !== $stat['ino'] || $opened['size'] !== $stat['size'] || !is_string($content) || strlen($content) !== $stat['size']) {
                throw new \RuntimeException('Installer inputs changed while being read.');
            }
            return $content;
        } finally {
            fclose($stream);
        }
    }
}
