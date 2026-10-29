<?php

declare(strict_types=1);

namespace Wcp360\Shared;

enum Surface: string
{
    case User = 'WCP-USER';
    case Admin = 'WCP-ADMIN';
    case Policy = 'POLICY';
}
