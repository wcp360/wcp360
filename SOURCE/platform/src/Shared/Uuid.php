<?php

declare(strict_types=1);

namespace Wcp360\Shared;

final readonly class Uuid
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    public function __construct(public string $value)
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw new ContractViolation('invalid UUID');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
