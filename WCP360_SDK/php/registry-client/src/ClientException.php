<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

final class ClientException extends \RuntimeException
{
    public function __construct(public readonly string $errorCode, public readonly ?int $httpStatus = null)
    {
        // Do not include request URLs, query values, upstream HTML or transport diagnostics.
        parent::__construct('WCP360 Registry: ' . $errorCode);
    }
}
