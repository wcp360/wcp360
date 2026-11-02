<?php

declare(strict_types=1);

namespace Wcp360\Database;

use Wcp360\Shared\ContractViolation;

final readonly class DatabaseColumn
{
    public function __construct(
        public string $name,
        public string $type,
        public string $nullability,
        public string $physicalRule,
    ) {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $name) || $type === '' || !in_array($nullability, ['NULL', 'NOT NULL'], true)) {
            throw new ContractViolation('invalid canonical database column definition');
        }
    }
}
