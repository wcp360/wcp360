<?php

declare(strict_types=1);
namespace Wcp360\Core\Shared\Security;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
final readonly class ContextUserChecker implements UserCheckerInterface
{
    public function __construct(private RuntimeContext $runtime) {}
    public function checkPreAuth(UserInterface $user): void { $this->checkPostAuth($user); }
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Principal || !$user->active || $user->context !== $this->runtime->context) throw new CustomUserMessageAccountStatusException('Account unavailable in this context.');
    }
}
