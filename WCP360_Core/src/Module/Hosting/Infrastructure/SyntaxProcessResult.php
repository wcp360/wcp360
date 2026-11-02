<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class SyntaxProcessResult
{
    public function __construct(public string $status, public string $stdout = '') {}
}
