<?php

declare(strict_types=1);

namespace Wcp360\CanonicalActions;

enum CanonicalPortProfile: string
{
    case Read = 'READ';
    case Mutation = 'MUTATION';
    case Managed = 'MANAGED';
}
