<?php

declare(strict_types=1);

namespace Wcp360\Security;

enum AuthenticationProfile: string
{
    case User = 'AUTH-U';
    case Admin = 'AUTH-A';
    case Both = 'AUTH-B';
    case Policy = 'AUTH-P';
    case Privileged = 'AUTH-PRIV';
    case Emergency = 'AUTH-E';
}
