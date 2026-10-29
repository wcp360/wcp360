<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use JsonException;
use Wcp360\Shared\ContractViolation;

final class ActionCatalog
{
    /** @var array<string,string> action_id => domain */
    private array $actions = [];

    /** @param iterable<string> $actionIds */
    public function __construct(iterable $actionIds)
    {
        foreach ($actionIds as $actionId) {
            if (!preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)+$/', $actionId)) {
                throw new ContractViolation('invalid canonical action id');
            }
            if (isset($this->actions[$actionId])) {
                throw new ContractViolation('duplicate canonical action id');
            }
            $this->actions[$actionId] = explode('.', $actionId, 2)[0];
        }
    }

    public static function fromApiContracts(ApiContractRegistry $contracts): self
    {
        return new self(array_map(static fn(ApiContractDefinition $c): string => $c->actionId, $contracts->all()));
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ContractViolation('action catalogue file missing');
        }
        try {
            /** @var array<string,mixed> $data */
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('action catalogue JSON invalid', 0, $e);
        }
        if (($data['version'] ?? null) !== '1.0' || !is_array($data['actions'] ?? null)) {
            throw new ContractViolation('action catalogue metadata mismatch');
        }
        $catalog = new self(array_map(static fn(array $row): string => (string) ($row['action_id'] ?? ''), $data['actions']));
        if (($data['count'] ?? null) !== $catalog->count() || ($data['domain_count'] ?? null) !== count($catalog->domains())) {
            throw new ContractViolation('action catalogue declared counts mismatch');
        }
        return $catalog;
    }

    public function has(string $actionId): bool
    {
        return isset($this->actions[$actionId]);
    }

    public function require(string $actionId): void
    {
        if (!$this->has($actionId)) {
            throw new ContractViolation('action is not in canonical API catalogue');
        }
    }

    public function count(): int
    {
        return count($this->actions);
    }

    /** @return list<string> */
    public function domains(): array
    {
        $domains = array_values(array_unique($this->actions));
        sort($domains);
        return $domains;
    }
}
