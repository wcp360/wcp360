<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Wcp360\Core\Module\Hosting\Application\ApplicationCatalogueSource;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueEntry;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueSnapshot;

/** Fixed local editorial source. No URL, code, provider admission or install instruction is consumed. */
final readonly class CsvApplicationCatalogueSource implements ApplicationCatalogueSource
{
    public const MAX_BYTES = 1048576;
    public const MAX_ENTRIES = 2048;

    public function __construct(private string $path) {}

    public function read(): ApplicationCatalogueSnapshot
    {
        $file = null;
        $csv = null;
        try {
            if ($this->path === '' || str_contains($this->path, '://') || str_contains($this->path, "\0")) return ApplicationCatalogueSnapshot::failed();
            clearstatcache(true, $this->path);
            $before = @lstat($this->path);
            if ($before === false) return ApplicationCatalogueSnapshot::notConfigured();
            if (is_link($this->path) || ($before['mode'] & 0170000) !== 0100000) return ApplicationCatalogueSnapshot::failed();
            $file = @fopen($this->path, 'rb');
            if ($file === false) return ApplicationCatalogueSnapshot::failed();
            $opened = fstat($file);
            if ($opened === false || ($opened['mode'] & 0170000) !== 0100000
                || $opened['dev'] !== $before['dev'] || $opened['ino'] !== $before['ino']) return ApplicationCatalogueSnapshot::failed();
            $raw = stream_get_contents($file, self::MAX_BYTES + 1);
            if ($raw === false || $raw === '' || strlen($raw) > self::MAX_BYTES || preg_match('//u', $raw) !== 1) {
                return ApplicationCatalogueSnapshot::failed();
            }
            $payload = str_starts_with($raw, "\xef\xbb\xbf") ? substr($raw, 3) : $raw;
            self::requireCsvSyntax($payload);
            $csv = fopen('php://memory', 'w+b');
            if ($csv === false || fwrite($csv, $payload) !== strlen($payload) || !rewind($csv)) return ApplicationCatalogueSnapshot::failed();
            if (fgetcsv($csv, null, ';', '"', '') !== ApplicationCatalogueEntry::HEADERS) return ApplicationCatalogueSnapshot::failed();
            $entries = [];
            $names = [];
            while (($row = fgetcsv($csv, null, ';', '"', '')) !== false) {
                if (count($row) !== count(ApplicationCatalogueEntry::HEADERS) || count($entries) >= self::MAX_ENTRIES) {
                    return ApplicationCatalogueSnapshot::failed();
                }
                $fields = array_combine(ApplicationCatalogueEntry::HEADERS, $row);
                $entry = ApplicationCatalogueEntry::fromFields($fields);
                if (isset($names[$entry->normalizedName])) return ApplicationCatalogueSnapshot::failed();
                $names[$entry->normalizedName] = true;
                $entries[] = $entry;
            }
            if (!feof($csv)) return ApplicationCatalogueSnapshot::failed();
            return ApplicationCatalogueSnapshot::available($entries, hash('sha256', $raw), strlen($raw));
        } catch (\Throwable) {
            return ApplicationCatalogueSnapshot::failed();
        } finally {
            if (is_resource($csv)) fclose($csv);
            if (is_resource($file)) fclose($file);
        }
    }

    /** Reject malformed quoting that native fgetcsv otherwise tolerates. Bytes are already bounded. */
    private static function requireCsvSyntax(string $payload): void
    {
        $state = 'start';
        $length = strlen($payload);
        for ($index = 0; $index < $length; ++$index) {
            $byte = $payload[$index];
            if ($state === 'quoted') {
                if ($byte === '"') $state = 'closed';
                continue;
            }
            if ($state === 'closed') {
                if ($byte === '"') { $state = 'quoted'; continue; }
                if ($byte === ';' || $byte === "\r" || $byte === "\n") { $state = 'start'; continue; }
                throw new \InvalidArgumentException('Candidate catalogue CSV rejected.');
            }
            if ($byte === ';' || $byte === "\r" || $byte === "\n") { $state = 'start'; continue; }
            if ($byte === '"') {
                if ($state !== 'start') throw new \InvalidArgumentException('Candidate catalogue CSV rejected.');
                $state = 'quoted';
                continue;
            }
            $state = 'unquoted';
        }
        if ($state === 'quoted') throw new \InvalidArgumentException('Candidate catalogue CSV rejected.');
    }
}
