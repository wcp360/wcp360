<?php

declare(strict_types=1);

namespace Wcp360\Database;

use Wcp360\Shared\ContractViolation;

final readonly class Revision
{
    public function __construct(public int $value)
    {
        if ($value < 1) {
            throw new ContractViolation('revision must be >= 1');
        }
    }

    public function next(): self
    {
        if ($this->value === PHP_INT_MAX) {
            throw new ContractViolation('revision overflow');
        }
        return new self($this->value + 1);
    }
}
