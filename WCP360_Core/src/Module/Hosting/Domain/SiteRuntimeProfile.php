<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Platform\WebStackCatalogue;

/** Existing durable topologies only; not authority to select Modern or activate a site. */
#[Exclude]
final readonly class SiteRuntimeProfile
{
    private function __construct(public ApplicationServer $server, public string $stack, public int $revision) {}

    public static function forServer(ApplicationServer $server): self
    {
        return new self($server, WebStackCatalogue::forApplicationServer($server->value), 1);
    }

    /** @param array<array-key,mixed> $row */
    public static function fromStorage(array $row): self
    {
        $server = $row['application_server'] ?? null;
        $stack = $row['web_stack'] ?? null;
        $revision = $row['stack_revision'] ?? null;
        if (!is_string($server) || !is_string($stack) || !in_array($revision, [1, '1'], true)) self::invalid();
        $engine = ApplicationServer::tryFrom($server);
        if ($engine === null) self::invalid();
        $profile = self::forServer($engine);
        if ($stack !== $profile->stack) self::invalid();

        return $profile;
    }

    /** @return array{web_stack:string,stack_revision:int} */
    public function storage(): array
    {
        return ['web_stack' => $this->stack, 'stack_revision' => $this->revision];
    }

    public function matches(self $other): bool
    {
        return $this->server === $other->server && $this->stack === $other->stack && $this->revision === $other->revision;
    }

    private static function invalid(): never
    {
        throw new \InvalidArgumentException('Candidate site runtime profile rejected.');
    }
}
