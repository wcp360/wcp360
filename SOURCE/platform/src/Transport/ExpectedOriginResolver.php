<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Shared\Surface;

interface ExpectedOriginResolver
{
    public function expectedOrigin(Surface $surface): string;
}
