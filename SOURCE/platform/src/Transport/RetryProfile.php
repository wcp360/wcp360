<?php

declare(strict_types=1);

namespace Wcp360\Transport;

enum RetryProfile: string
{
    case Read = 'RR';
    case KeyProtected = 'RK';
    case NoBlindRetry = 'RN';
}
