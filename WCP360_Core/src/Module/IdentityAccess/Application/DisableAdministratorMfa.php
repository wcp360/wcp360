<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

/** Factor disablement and current-context session revocation commit together. */
final readonly class DisableAdministratorMfa
{
    public function __construct(private CandidateMfa $mfa, private CandidateSessions $sessions, private WorkbenchStore $store) {}

    public function disable(string $actor): void
    {
        $this->store->db->transactional(function () use ($actor): void {
            $this->mfa->reset($actor);
            $this->sessions->revokeAll($actor, 'admin');
        });
    }
}
