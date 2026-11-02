<?php

declare(strict_types=1);

namespace Wcp360\Database;

use JsonException;
use Wcp360\Shared\ContractViolation;

final class DatabaseCatalog
{
    /** @var array<string,DatabaseTable> */
    private array $tables = [];

    /** @param list<DatabaseTable> $tables */
    private function __construct(array $tables)
    {
        foreach ($tables as $table) {
            if (isset($this->tables[$table->name])) {
                throw new ContractViolation('duplicate canonical database table');
            }
            $this->tables[$table->name] = $table;
        }
        if (count($this->tables) !== 54) {
            throw new ContractViolation('canonical database table count mismatch');
        }
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ContractViolation('database catalogue file missing');
        }
        try {
            /** @var array<string,mixed> $data */
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('database catalogue JSON invalid', 0, $e);
        }
        if (($data['version'] ?? null) !== 'v1.0' || ($data['count'] ?? null) !== 54 || !is_array($data['tables'] ?? null)) {
            throw new ContractViolation('database catalogue metadata mismatch');
        }
        $tables = [];
        foreach ($data['tables'] as $row) {
            if (!is_array($row) || !is_array($row['columns'] ?? null)) {
                throw new ContractViolation('invalid database catalogue row');
            }
            $columns = [];
            foreach ($row['columns'] as $column) {
                $columns[] = new DatabaseColumn(
                    (string) ($column['name'] ?? ''),
                    (string) ($column['type'] ?? ''),
                    (string) ($column['nullability'] ?? ''),
                    (string) ($column['physical_rule'] ?? ''),
                );
            }
            $tables[] = new DatabaseTable((string) ($row['table'] ?? ''), $columns);
        }
        return new self($tables);
    }

    public function count(): int
    {
        return count($this->tables);
    }

    public function require(string $name): DatabaseTable
    {
        return $this->tables[$name] ?? throw new ContractViolation('unknown canonical database table');
    }
}
