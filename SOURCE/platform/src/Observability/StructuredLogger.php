<?php

declare(strict_types=1);

namespace Wcp360\Observability;

interface StructuredLogger
{
    public function write(StructuredLogRecord $record): void;
}
