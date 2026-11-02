<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use Wcp360\Shared\ContractViolation;

final class RequestSchema
{
    /** @return array{0:list<string>,1:list<string>} */
    public static function parse(string $schema): array
    {
        $schema = trim($schema);
        if (strlen($schema) < 2 || $schema[0] !== '{' || $schema[-1] !== '}') {
            throw new ContractViolation('unsupported API request schema envelope');
        }
        $inner = trim(substr($schema, 1, -1));
        if ($inner === '') {
            return [[], []];
        }

        $required = [];
        $optional = [];
        foreach (explode(',', $inner) as $raw) {
            $field = trim($raw);
            $isOptional = str_ends_with($field, '?');
            if ($isOptional) {
                $field = substr($field, 0, -1);
            }
            if (str_ends_with($field, '[]')) {
                $field = substr($field, 0, -2);
            }
            if (!preg_match('/^[a-z][a-z0-9_]*$/', $field)) {
                throw new ContractViolation('unsupported API request schema field');
            }
            if (in_array($field, $required, true) || in_array($field, $optional, true)) {
                throw new ContractViolation('duplicate API request schema field');
            }
            if ($isOptional) {
                $optional[] = $field;
            } else {
                $required[] = $field;
            }
        }
        return [$required, $optional];
    }

    /** @param array<string,mixed> $payload */
    public static function validateFieldNames(ApiContractDefinition $contract, array $payload): void
    {
        foreach ($contract->requiredFields as $field) {
            if (!array_key_exists($field, $payload)) {
                throw new ContractViolation('required API request field missing: ' . $field);
            }
        }
        $allowed = array_fill_keys([...$contract->requiredFields, ...$contract->optionalFields], true);
        foreach ($payload as $field => $_) {
            if (!isset($allowed[$field])) {
                throw new ContractViolation('unknown API request field: ' . $field);
            }
        }
    }
}
