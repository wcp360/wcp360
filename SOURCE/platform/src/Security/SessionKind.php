<?php

declare(strict_types=1);

namespace Wcp360\Security;

enum SessionKind: string
{
    case User = 'USER';
    case Admin = 'ADMIN';
    case PrivilegedAdmin = 'PRIVILEGED_ADMIN';
    case BreakGlass = 'BREAK_GLASS';
}
