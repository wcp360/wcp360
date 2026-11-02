<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Symfony\Component\Process\Process;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;

/** Fixed non-root Nginx syntax probe; no listener or service is started. */
final readonly class NginxSyntaxProbe
{
    private const TEMP_DIRECTORIES = ['body', 'proxy', 'fastcgi', 'uwsgi', 'scgi'];
    private const TEMP_FILES = ['artifact.conf', 'nginx.conf', 'nginx.pid'];

    public function __construct(private SyntaxProcessRunner $runner, private NativeSyntaxTools $tools) {}

    public function check(string $content): string
    {
        $binary = $this->tools->binary('nginx');
        if ($binary === null) return 'not_configured';
        if ($content === '' || strlen($content) > PreparedPlanDocument::MAX_CONTENT_BYTES
            || preg_match('//u', $content) !== 1
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $content) === 1) return 'failed';

        // The tool provider admits Linux/non-root only. Never interpolate TMPDIR
        // or caller paths into wrappers; the generated artifact remains byte-for-byte.
        $directory = '/tmp/wcp360-native-syntax-'.bin2hex(random_bytes(16));
        $created = false;
        $status = 'failed';
        try {
            if (!@mkdir($directory, 0700)) throw new \RuntimeException('Private syntax directory unavailable.');
            $created = true;
            $this->requireDirectory($directory);
            foreach (self::TEMP_DIRECTORIES as $name) {
                if (!@mkdir($directory.'/'.$name, 0700)) throw new \RuntimeException('Private syntax directory unavailable.');
                $this->requireDirectory($directory.'/'.$name);
            }
            $this->write($directory.'/artifact.conf', $content);
            $http = "access_log off; client_body_temp_path $directory/body; proxy_temp_path $directory/proxy; "
                ."fastcgi_temp_path $directory/fastcgi; uwsgi_temp_path $directory/uwsgi; scgi_temp_path $directory/scgi; "
                ."include $directory/artifact.conf;";
            $this->write($directory.'/nginx.conf', "pid $directory/nginx.pid; error_log stderr; "
                ."events { worker_connections 8; } http { $http }\n");
            $process = new Process([$binary, '-t', '-p', $directory.'/', '-c', $directory.'/nginx.conf'], $directory);
            $result = $this->runner->run($process);
            $status = $result->status === 'passed' ? 'passed' : 'failed';
        } catch (\Throwable) {
            // Child output, generated content, temporary paths and exception details stay private.
            $status = 'failed';
        } finally {
            if ($created && !$this->cleanup($directory)) $status = 'failed';
        }

        return $status;
    }

    private function requireDirectory(string $path): void
    {
        clearstatcache(true, $path);
        $stat = @lstat($path);
        if ($stat === false || ($stat['mode'] & 0170000) !== 0040000 || ($stat['mode'] & 0777) !== 0700
            || realpath($path) !== $path) {
            throw new \RuntimeException('Private syntax directory rejected.');
        }
    }

    private function write(string $path, string $content): void
    {
        $handle = @fopen($path, 'xb');
        if ($handle === false) throw new \RuntimeException('Private syntax file unavailable.');
        try {
            if (!@chmod($path, 0600) || fwrite($handle, $content) !== strlen($content)) {
                throw new \RuntimeException('Private syntax file unavailable.');
            }
        } finally {
            fclose($handle);
        }
    }

    /** Never follows links or recursively removes unexpected content. */
    private function cleanup(string $directory): bool
    {
        try {
            $this->requireDirectory($directory);
            foreach (self::TEMP_FILES as $name) {
                $path = $directory.'/'.$name;
                clearstatcache(true, $path);
                $stat = @lstat($path);
                if ($stat === false) continue;
                if (($stat['mode'] & 0170000) !== 0100000 || !@unlink($path)) return false;
            }
            foreach (self::TEMP_DIRECTORIES as $name) {
                $path = $directory.'/'.$name;
                clearstatcache(true, $path);
                if (@lstat($path) === false) continue;
                $this->requireDirectory($path);
                if (!@rmdir($path)) return false;
            }

            return @rmdir($directory);
        } catch (\Throwable) {
            return false;
        }
    }
}
