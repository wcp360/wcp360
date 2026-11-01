<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\HttpFoundation\Request;

/** Existing workbench transport only; Accept/Authorization never selects a surface. */
final class CandidateApiRequest
{
    public static function matches(Request $request): bool
    {
        $path = $request->getPathInfo();

        return $path === '/workbench/v1' || str_starts_with($path, '/workbench/v1/');
    }
}
