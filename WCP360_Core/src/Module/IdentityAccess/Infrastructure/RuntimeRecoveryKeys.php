<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\IdentityAccess\Domain\RecoveryKeySource;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Read only the requested permitted surface; no APP_SECRET/MFA/opposite-key fallback. */
final readonly class RuntimeRecoveryKeys implements RecoveryKeySource
{
    public function __construct(private RuntimeContext $runtime) {}

    public function forSurface(string $surface): string
    {
        if (!in_array($surface, ['user','admin'], true)
            || !in_array($this->runtime->context, ['setup',$surface], true)) return '';
        $name = 'WCP_RECOVERY_'.strtoupper($surface).'_KEY';
        $value = $_SERVER[$name] ?? $_ENV[$name] ?? getenv($name);
        return is_string($value) && PasswordRecoveryCode::configured($value) ? $value : '';
    }
}
