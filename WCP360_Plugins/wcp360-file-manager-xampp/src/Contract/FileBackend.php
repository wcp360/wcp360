<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Contract;
interface FileBackend
{
    /** Paths are relative to a server-resolved tenant root. Never accept a root from the browser.
     * Operations: list, read, download, create, save, mkdir, rename, upload, trash, trash_list, restore, copy, properties; pro operations documented in docs/PRO-TOOLS.md.
     * download returns content (bytes), name; never a host filesystem path.
     * Implementations must enforce tenant authorization independently of the controller.
     */
    public function execute(Identity $identity, string $operation, array $input): array;
}
