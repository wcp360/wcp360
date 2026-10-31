<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\IdentityAccess\Domain;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
final readonly class Principal implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(public string $id, public string $email, private string $passwordHash, public string $context, public bool $active = true) {}
    public function getUserIdentifier(): string { if($this->email==='')throw new \UnexpectedValueException('Empty identity.');return $this->email; }
    public function getPassword(): string { return $this->passwordHash; }
    public function getRoles(): array { return [$this->context === 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER']; }
    public function __serialize(): array
    {
        return ['id'=>$this->id,'email'=>$this->email,'passwordHash'=>hash('crc32c',$this->passwordHash),'context'=>$this->context,'active'=>$this->active];
    }
    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        foreach(['id','email','passwordHash','context']as $key)if(!isset($data[$key])||!is_string($data[$key])||$data[$key]==='')throw new \UnexpectedValueException('Malformed session identity.');
        if(!isset($data['active'])||!is_bool($data['active'])||!in_array($data['context'],['user','admin'],true))throw new \UnexpectedValueException('Malformed session context.');
        $this->id=$data['id'];$this->email=$data['email'];$this->passwordHash=$data['passwordHash'];$this->context=$data['context'];$this->active=$data['active'];
    }
    public function eraseCredentials(): void {}
}
