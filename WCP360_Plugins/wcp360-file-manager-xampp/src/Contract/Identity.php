<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Contract;
final readonly class Identity
{
    public function __construct(public string $tenantId, public string $subjectId, public bool $canWrite, public bool $canManage = false) {}
}
