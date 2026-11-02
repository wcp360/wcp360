<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

enum OperationKind: string
{
    case Read = 'READ';
    case Mutation = 'MUTATION';
}
