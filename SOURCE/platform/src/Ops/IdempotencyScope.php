<?php

declare(strict_types=1);

namespace Wcp360\Ops;

enum IdempotencyScope: string
{
    case Global = 'GLOBAL';
    case Account = 'ACCOUNT';
    case Tenant = 'TENANT';
}
