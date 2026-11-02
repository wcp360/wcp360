<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
final class PairingException extends \RuntimeException
{
    public function __construct(public readonly string $errorCode,public readonly ?int $httpStatus=null){parent::__construct($errorCode);}
}
