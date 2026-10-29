<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\CanonicalErrorCode;

final readonly class AuthorizationDecision
{
    private function __construct(
        public bool $allowed,
        public ?CanonicalErrorCode $errorCode,
        public string $reason,
    ) {
    }

    public static function allow(): self
    {
        return new self(true, null, 'allowed');
    }

    public static function deny(CanonicalErrorCode $code, string $reason): self
    {
        return new self(false, $code, $reason);
    }
}
