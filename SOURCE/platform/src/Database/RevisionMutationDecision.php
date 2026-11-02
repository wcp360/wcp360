<?php

declare(strict_types=1);

namespace Wcp360\Database;

use Wcp360\Shared\CanonicalErrorCode;

final readonly class RevisionMutationDecision
{
    private function __construct(
        public bool $allowed,
        public ?Revision $nextRevision,
        public ?CanonicalErrorCode $errorCode,
    ) {
    }

    public static function evaluate(Revision $expected, Revision $current): self
    {
        if ($expected->value !== $current->value) {
            return new self(false, null, CanonicalErrorCode::ConcurrencyConflict);
        }
        return new self(true, $current->next(), null);
    }
}
