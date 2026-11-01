<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/** Existing cookie authentication only. This does not introduce bearer-token authentication. */
final readonly class CandidateAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(private HttpErrorResponder $responder) {}

    public function start(#[\SensitiveParameter] Request $request, #[\SensitiveParameter] ?AuthenticationException $authException = null): Response
    {
        return CandidateApiRequest::matches($request)
            ? $this->responder->respond($request, HttpFailure::fromStatus(401))
            : new RedirectResponse('/login');
    }
}
