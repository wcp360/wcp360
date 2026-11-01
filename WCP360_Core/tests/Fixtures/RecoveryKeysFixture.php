<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Fixtures;

use Wcp360\Core\Module\IdentityAccess\Domain\RecoveryKeySource;

final readonly class RecoveryKeysFixture implements RecoveryKeySource
{
    public function __construct(private string $user, private ?string $admin = null) {}
    public function forSurface(string $surface): string
    {
        return match ($surface) { 'user' => $this->user, 'admin' => $this->admin ?? $this->user, default => '' };
    }
}
