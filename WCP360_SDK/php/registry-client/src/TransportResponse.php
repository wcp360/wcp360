<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

final class TransportResponse
{
    public function __construct(public readonly int $status, public readonly array $headers, public readonly string $body) {}
    public function header(string $name): string
    {
        foreach ($this->headers as $key => $value) {
            if (is_string($key) && strcasecmp($key, $name) === 0 && is_string($value)) return $value;
        }
        return '';
    }
}
