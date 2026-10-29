<?php

declare(strict_types=1);

namespace Wcp360\Database;

use Wcp360\Shared\ContractViolation;

final class DatabaseTable
{
    /** @var array<string,DatabaseColumn> */
    private array $columns = [];

    /** @param list<DatabaseColumn> $columns */
    public function __construct(public readonly string $name, array $columns)
    {
        if (!preg_match('/^wcp_[a-z0-9_]+\.[a-z][a-z0-9_]*$/', $name) || $columns === []) {
            throw new ContractViolation('invalid canonical database table definition');
        }
        foreach ($columns as $column) {
            if (isset($this->columns[$column->name])) {
                throw new ContractViolation('duplicate canonical database column');
            }
            $this->columns[$column->name] = $column;
        }
    }

    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    public function requireColumn(string $name): DatabaseColumn
    {
        return $this->columns[$name] ?? throw new ContractViolation('unknown canonical database column');
    }

    public function columnCount(): int
    {
        return count($this->columns);
    }
}
