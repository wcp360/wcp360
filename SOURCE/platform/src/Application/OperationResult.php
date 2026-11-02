<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Wcp360\Shared\CanonicalErrorCode;

final readonly class OperationResult
{
    /** @param array<string,mixed> $data */
    private function __construct(
        public bool $ok,
        public array $data,
        public ?CanonicalErrorCode $errorCode,
    ) {
    }

    /** @param array<string,mixed> $data */
    public static function success(array $data): self
    {
        return new self(true, $data, null);
    }

    public static function failure(CanonicalErrorCode $code): self
    {
        return new self(false, [], $code);
    }
}
