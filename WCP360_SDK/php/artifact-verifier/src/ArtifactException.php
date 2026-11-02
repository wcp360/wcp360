<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;
final class ArtifactException extends \RuntimeException
{
    public function __construct(public readonly string $errorCode) { parent::__construct('WCP360 artifact: '.$errorCode); }
}
