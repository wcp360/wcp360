<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;

final readonly class PermissionDefinition
{
    /** @param list<Surface> $surfaces */
    public function __construct(
        public string $id,
        public array $surfaces,
        public string $sourceSurface,
        public string $status,
    ) {
        if ($id === '' || str_contains($id, "\0")) {
            throw new ContractViolation('invalid permission id');
        }
        if ($status !== 'ACTIVE') {
            throw new ContractViolation('inactive permission cannot be loaded into active registry');
        }
        if ($surfaces === []) {
            throw new ContractViolation('permission has no allowed surface');
        }
    }

    /** @param array{id:string,surface:string,status:string} $row */
    public static function fromArray(array $row): self
    {
        return new self(
            $row['id'],
            self::parseSurfaces($row['surface']),
            $row['surface'],
            $row['status'],
        );
    }

    public function allowsSurface(Surface $surface): bool
    {
        foreach ($this->surfaces as $candidate) {
            if ($candidate === $surface) {
                return true;
            }
        }
        return false;
    }

    /** @return list<Surface> */
    private static function parseSurfaces(string $surface): array
    {
        $upper = strtoupper($surface);
        if (str_starts_with($upper, 'BOTH')) {
            return [Surface::User, Surface::Admin];
        }
        if (str_starts_with($upper, 'WCP-USER')) {
            return [Surface::User];
        }
        if (str_starts_with($upper, 'WCP-ADMIN')) {
            return [Surface::Admin];
        }
        if (str_starts_with($upper, 'POLICY')) {
            return [Surface::Policy, Surface::Emergency];
        }
        throw new ContractViolation('unknown permission surface');
    }
}
