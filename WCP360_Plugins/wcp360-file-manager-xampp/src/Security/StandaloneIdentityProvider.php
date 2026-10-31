<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Security;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wcp360\FileManager\Contract\{Identity,IdentityProvider};
final readonly class StandaloneIdentityProvider implements IdentityProvider
{
    public function __construct(private Security $security) {}
    public function current(): Identity
    {
        $user = $this->security->getUser();
        if (!$user || !$this->security->isGranted('ROLE_FILE_MANAGER')) { throw new AccessDeniedException(); }
        return new Identity('standalone', $user->getUserIdentifier(), $this->security->isGranted('ROLE_FILE_MANAGER_WRITE'), $this->security->isGranted('ROLE_FILE_MANAGER_ADMIN'));
    }
}
