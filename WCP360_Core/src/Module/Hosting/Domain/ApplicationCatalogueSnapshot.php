<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class ApplicationCatalogueSnapshot
{
    /** @param list<ApplicationCatalogueEntry> $entries */
    private function __construct(public string $status, public array $entries, public ?string $sha256, public ?int $bytes) {}

    /** @param array<array-key,mixed> $entries */
    public static function available(array $entries, string $sha256, int $bytes): self
    {
        if (!array_is_list($entries) || count($entries) > 2048 || preg_match('/^[a-f0-9]{64}$/D', $sha256) !== 1
            || $bytes < 1 || $bytes > 1048576) throw new \InvalidArgumentException('Candidate catalogue snapshot rejected.');
        $checked = [];
        foreach ($entries as $entry) {
            if (!$entry instanceof ApplicationCatalogueEntry) throw new \InvalidArgumentException('Candidate catalogue snapshot rejected.');
            $checked[] = $entry;
        }
        return new self('available', $checked, $sha256, $bytes);
    }

    public static function notConfigured(): self { return new self('not_configured', [], null, null); }
    public static function failed(): self { return new self('failed', [], null, null); }
}
