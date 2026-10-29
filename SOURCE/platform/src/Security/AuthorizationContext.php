<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\Surface;
use Wcp360\Shared\Uuid;

final readonly class AuthorizationContext
{
    /** @param list<string> $permissionIds */
    public function __construct(
        public Surface $surface,
        public Uuid $principalId,
        public ?Uuid $tenantId,
        public array $permissionIds,
        public bool $mfaSatisfied,
        public bool $privilegedContext,
    ) {
    }

    public function hasPermission(string $permissionId): bool
    {
        return in_array($permissionId, $this->permissionIds, true);
    }
}
