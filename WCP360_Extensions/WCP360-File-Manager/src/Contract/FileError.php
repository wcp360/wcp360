<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Contract;
final class FileError extends \RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message, public readonly int $httpStatus = 400)
    { parent::__construct($message); }
}
