<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** @implements UserProviderInterface<Principal> */
final readonly class PrincipalProvider implements UserProviderInterface
{
    public function __construct(private WorkbenchStore $store, private RuntimeContext $context) {}

    public function loadUserByIdentifier(string $identifier): Principal
    {
        if (!CandidateUsername::valid($identifier)) {
            throw new UserNotFoundException();
        }
        $row = $this->store->db->fetchAssociative(
            'SELECT * FROM wcp_candidate_users WHERE username = ? AND context = ?',
            [$identifier, $this->context->context],
        );
        if (!$row) {
            throw new UserNotFoundException();
        }

        return new Principal($row['id'], $row['username'], $row['email'], $row['password_hash'], $row['context'], (bool) $row['active']);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Principal) {
            throw new UnsupportedUserException();
        }
        if ($user->context !== $this->context->context || !CandidateUsername::valid($user->username)) {
            throw new UserNotFoundException();
        }
        $fresh = $this->loadUserByIdentifier($user->username);
        if (!$fresh->active || $fresh->id !== $user->id) {
            throw new UserNotFoundException();
        }

        return $fresh;
    }

    public function supportsClass(string $class): bool { return $class === Principal::class; }
}
