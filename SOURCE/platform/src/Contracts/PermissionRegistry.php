<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use JsonException;
use Wcp360\Shared\ContractViolation;

final class PermissionRegistry
{
    /** @var array<string,PermissionDefinition> */
    private array $permissions = [];

    /** @param iterable<PermissionDefinition> $definitions */
    public function __construct(iterable $definitions)
    {
        foreach ($definitions as $definition) {
            if (isset($this->permissions[$definition->id])) {
                throw new ContractViolation('duplicate permission id');
            }
            $this->permissions[$definition->id] = $definition;
        }
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ContractViolation('permission registry file missing');
        }
        try {
            /** @var array{version:string,count:int,permissions:list<array{id:string,surface:string,status:string}>} $data */
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('permission registry JSON invalid', 0, $e);
        }
        if (($data['version'] ?? null) !== 'v1.0') {
            throw new ContractViolation('permission registry version mismatch');
        }
        $defs = array_map(PermissionDefinition::fromArray(...), $data['permissions'] ?? []);
        if (($data['count'] ?? -1) !== count($defs)) {
            throw new ContractViolation('permission registry declared count mismatch');
        }
        return new self($defs);
    }

    public function count(): int
    {
        return count($this->permissions);
    }

    public function has(string $permissionId): bool
    {
        return isset($this->permissions[$permissionId]);
    }

    public function require(string $permissionId): PermissionDefinition
    {
        return $this->permissions[$permissionId]
            ?? throw new ContractViolation('unknown permission id');
    }
}
