<?php

declare(strict_types=1);

namespace Wcp360\Security;

final readonly class AuthorizationRequest
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        public string $permissionId,
        public string $actionId,
        public string $resourceType,
        public string $tenantScope,
        public array $payload,
    ) {
    }
}
