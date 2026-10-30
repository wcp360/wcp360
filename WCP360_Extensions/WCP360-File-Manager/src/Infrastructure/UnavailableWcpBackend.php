<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Infrastructure;
use Wcp360\FileManager\Contract\{FileBackend,FileError,Identity};
/** Fail closed until the WCP360 IPC contract and tenant executor are approved and implemented. */
final class UnavailableWcpBackend implements FileBackend
{
    public function execute(Identity $identity, string $operation, array $input): array
    { throw new FileError('WCP_ADAPTER_NOT_CONFIGURED', 'Adaptateur WCP360 non configuré.', 503); }
}
