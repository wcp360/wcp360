<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

enum Surface: string
{
    case User = 'WCP-USER';
    case Admin = 'WCP-ADMIN';
    case Policy = 'POLICY';
    case Emergency = 'EMERGENCY';
}
