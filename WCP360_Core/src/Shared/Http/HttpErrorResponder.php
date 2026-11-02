<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;

/** Presentation only: never authorizes, mutates a resource or retries an operation. */
final readonly class HttpErrorResponder
{
    public function __construct(private RequestReference $references, private Environment $twig,
        private ?LoggerInterface $logger = null) {}

    public function exception(#[\SensitiveParameter] Request $request, #[\SensitiveParameter] \Throwable $error): Response
    {
        return $this->respond($request, HttpFailure::fromException($error),
            $error instanceof HttpExceptionInterface ? $error->getHeaders() : []);
    }

    /** @param array<string,string|string[]> $headers Untrusted exception headers, narrowed below. */
    public function respond(#[\SensitiveParameter] Request $request, HttpFailure $failure, array $headers = []): Response
    {
        $reference = $this->references->get($request);
        $safeHeaders = $this->safeHeaders($headers);
        $safeHeaders[RequestReference::HEADER] = $reference;
        $safeHeaders['Cache-Control'] = 'no-store, private';
        $safeHeaders['Referrer-Policy'] = 'no-referrer';
        try {
            $this->logger?->log($failure->status >= 500 ? 'error' : 'notice', 'WCP360 HTTP request refused or failed.', [
                'request_id' => $reference, 'status' => $failure->status, 'error_code' => $failure->code,
            ]);
        } catch (\Throwable) {
            // Optional diagnostic logging is not the domain's mandatory durable audit.
        }
        if (CandidateApiRequest::matches($request)) {
            return new JsonResponse(['error' => ['code' => $failure->code, 'message' => $failure->message],
                'request_id' => $reference], $failure->status, $safeHeaders);
        }
        try {
            return new Response($this->twig->render('errors/http.html.twig', [
                'status_code' => $failure->status, 'error_title' => $failure->title,
                'error_message' => $failure->explanation, 'reference' => $reference,
            ]), $failure->status, $safeHeaders);
        } catch (\Throwable) {
            // Template/asset/container failure must not recurse through the debug renderer.
            return new Response("WCP360: request could not be completed.\nReference: ".$reference."\n",
                $failure->status, $safeHeaders + ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
    }

    /** @param array<string,string|string[]> $headers
     * @return array<string,string>
     */
    private function safeHeaders(array $headers): array
    {
        $safe = [];
        foreach ($headers as $name => $value) {
            if (!is_string($value)) continue;
            if (strcasecmp($name, 'Retry-After') === 0 && preg_match('/\A[0-9]{1,6}\z/D', $value) === 1) {
                $safe['Retry-After'] = $value;
            }
            if (strcasecmp($name, 'Allow') === 0
                && preg_match('/\A(?:GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS)(?:, ?(?:GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS))*\z/D', $value) === 1) {
                $safe['Allow'] = $value;
            }
        }

        return $safe;
    }
}
