<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use JsonException;
use Wcp360\Shared\ContractViolation;

final class ApiContractRegistry
{
    /** @var array<string,ApiContractDefinition> */
    private array $contracts = [];

    /** @param iterable<ApiContractDefinition> $definitions */
    public function __construct(iterable $definitions)
    {
        foreach ($definitions as $definition) {
            if (isset($this->contracts[$definition->contractId])) {
                throw new ContractViolation('duplicate API contract id');
            }
            $this->contracts[$definition->contractId] = $definition;
        }
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ContractViolation('API contract registry file missing');
        }
        try {
            /** @var array{version:string,count:int,contracts:list<array<string,mixed>>} $data */
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('API contract registry JSON invalid', 0, $e);
        }
        if (($data['version'] ?? null) !== '1.0') {
            throw new ContractViolation('API contract registry version mismatch');
        }
        $defs = array_map(ApiContractDefinition::fromArray(...), $data['contracts'] ?? []);
        if (($data['count'] ?? -1) !== count($defs)) {
            throw new ContractViolation('API contract declared count mismatch');
        }
        return new self($defs);
    }

    public function count(): int
    {
        return count($this->contracts);
    }

    public function require(string $contractId): ApiContractDefinition
    {
        return $this->contracts[$contractId]
            ?? throw new ContractViolation('unknown API contract id');
    }
}
