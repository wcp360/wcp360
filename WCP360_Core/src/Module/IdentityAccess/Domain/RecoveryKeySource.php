<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Domain;

/** Candidate context-scoped secret input; not a general secret-store interface. */
interface RecoveryKeySource
{
    public function forSurface(string $surface): string;
}
