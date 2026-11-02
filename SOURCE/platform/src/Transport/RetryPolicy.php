<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Shared\CanonicalErrorCode;

final class RetryPolicy
{
    public static function mayRetryTransport(
        RetryProfile $profile,
        int $attempt,
        bool $responseReceived,
        ?CanonicalErrorCode $errorCode,
    ): bool {
        if ($attempt < 0) {
            return false;
        }
        if ($profile !== RetryProfile::Read || $attempt >= 2) {
            return false;
        }
        if (!$responseReceived) {
            return true;
        }
        return in_array($errorCode, [CanonicalErrorCode::RateLimited, CanonicalErrorCode::ServiceUnavailable], true);
    }

    public static function requiresReconciliationBeforeExecutionRetry(RetryProfile $profile, CanonicalErrorCode $errorCode): bool
    {
        return $profile === RetryProfile::NoBlindRetry && $errorCode === CanonicalErrorCode::UnknownOutcome;
    }
}
