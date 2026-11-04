<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Wcp360\Core\Shared\Http\CandidateApiRequest;
use Wcp360\Core\Shared\Http\HttpErrorResponder;
use Wcp360\Core\Shared\Http\HttpErrorSubscriber;
use Wcp360\Core\Shared\Http\RequestReference;

final class HttpErrorBoundaryTest extends TestCase
{
    public function testFreshServerReferencesIgnoreHeadersAndDoNotSurviveAsWorkerState(): void
    {
        $references = new RequestReference();
        $responder = new HttpErrorResponder($references, new Environment(new ArrayLoader()));
        $subscriber = new HttpErrorSubscriber($references, $responder, new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage());
        $kernel = $this->createStub(HttpKernelInterface::class);
        $seen = [];
        foreach (['user.localhost', 'admin.localhost'] as $host) {
            $request = Request::create('http://'.$host.'/health', server: [
                'HTTP_X_WCP360_REQUEST_ID' => 'private-caller-marker', 'HTTP_X_REQUEST_ID' => 'private-caller-marker',
                'HTTP_TRACEPARENT' => 'private-caller-marker',
            ]);
            $request->attributes->set('_wcp_candidate_request_reference', str_repeat('a', 32));
            $subscriber->begin(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
            $response = new Response(headers: [RequestReference::HEADER => 'private-caller-marker']);
            $subscriber->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));
            $id = $response->headers->get(RequestReference::HEADER);
            self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $id);
            self::assertNotSame(str_repeat('a', 32), $id);
            self::assertSame($id, $references->get($request));
            $seen[] = $id;
            // A subrequest cannot replace the main request's reference.
            $subscriber->begin(new RequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST));
            self::assertSame($id, $references->get($request));
        }
        self::assertNotSame($seen[0], $seen[1]);
    }

    public function testWellFormedUnissuedRequestReferenceCannotBecomeAnErrorReference(): void
    {
        $references = new RequestReference();
        $request = Request::create('/workbench/v1/plans', server: [
            'HTTP_X_WCP360_REQUEST_ID' => str_repeat('b', 32),
        ]);
        $forged = str_repeat('a', 32);
        $request->attributes->set('_wcp_candidate_request_reference', $forged);

        // The response/error path can run before the main request listener.
        $issued = $references->get($request);
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $issued);
        self::assertNotSame($forged, $issued);
        self::assertNotSame(str_repeat('b', 32), $issued);
        self::assertSame($issued, $request->attributes->get('_wcp_candidate_request_reference'));
        self::assertSame($issued, $references->get($request));
    }

    public function testDownstreamReferenceAttributeTamperingCannotReplaceIssuedValue(): void
    {
        $references = new RequestReference();
        $first = Request::create('/workbench/v1/plans');
        $issued = $references->begin($first);
        $first->attributes->set('_wcp_candidate_request_reference', str_repeat('c', 32));

        self::assertSame($issued, $references->get($first));
        self::assertSame($issued, $first->attributes->get('_wcp_candidate_request_reference'));

        $second = Request::create('/workbench/v1/plans');
        $second->attributes->set('_wcp_candidate_request_reference', $issued);
        self::assertNotSame($issued, $references->get($second));
    }

    public function testRepeatedInitializationPreservesOneServerIssuedRequestReference(): void
    {
        $refs = new RequestReference();
        $request = Request::create('/workbench/v1/plans', server: [
            'HTTP_X_WCP360_REQUEST_ID' => str_repeat('d', 32),
        ]);
        $request->attributes->set('_wcp_candidate_request_reference', str_repeat('e', 32));

        $issued = $refs->get($request);
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $issued);
        self::assertNotSame(str_repeat('d', 32), $issued);
        self::assertNotSame(str_repeat('e', 32), $issued);

        $request->attributes->set('_wcp_candidate_request_reference', str_repeat('f', 32));
        self::assertSame($issued, $refs->begin($request));
        self::assertSame($issued, $refs->begin($request));
        self::assertSame($issued, $request->attributes->get('_wcp_candidate_request_reference'));
    }

    public function testEarlyDiagnosticReferenceSurvivesMainListenerAndResponse(): void
    {
        $refs = new RequestReference();
        $responder = new HttpErrorResponder($refs, new Environment(new ArrayLoader()));
        $subscriber = new HttpErrorSubscriber($refs, $responder,
            new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage());
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('http://user.localhost/workbench/v1/plans');
        $early = $refs->get($request);

        // A later begin() on the same main Request must not break a previously
        // logged diagnostic reference or the final response header.
        $subscriber->begin(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        $response = new Response(headers: [RequestReference::HEADER => str_repeat('a', 32)]);
        $subscriber->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        self::assertSame($early, $response->headers->get(RequestReference::HEADER));
        self::assertSame($early, $refs->get($request));

        $newRequest = Request::create('http://user.localhost/workbench/v1/plans');
        self::assertNotSame($early, $refs->begin($newRequest));
    }

    public function testNamespaceDeterminesTheTransportWithoutAcceptHeaderOrPrefixConfusion(): void
    {
        self::assertTrue(CandidateApiRequest::matches(Request::create('/workbench/v1/plans')));
        self::assertTrue(CandidateApiRequest::matches(Request::create('/workbench/v1')));
        foreach (['/login', '/reset-password', '/workbench/v10/plans', '/workbench/v1-copy'] as $path) {
            self::assertFalse(CandidateApiRequest::matches(Request::create($path, server: ['HTTP_ACCEPT' => 'application/json'])));
        }
    }

    public function testExceptionHeadersAndOptionalDiagnosticLogContainOnlySafeMetadata(): void
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->records[] = [$level, (string) $message, $context];
            }
        };
        $responder = new HttpErrorResponder(new RequestReference(), new Environment(new ArrayLoader()), $logger);
        $request = Request::create('/workbench/v1/plans?password=private-marker', 'POST', ['secret' => 'private-marker']);
        $error = new HttpException(429, 'private-marker driver sql /srv/private', headers: [
            'Retry-After' => '30', 'Allow' => 'POST, OPTIONS', 'X-Private-Debug' => 'private-marker',
            'Location' => 'https://private-marker.test', 'Set-Cookie' => 'private-marker',
        ]);
        $response = $responder->exception($request, $error);
        self::assertSame(429, $response->getStatusCode());
        self::assertSame('30', $response->headers->get('Retry-After'));
        self::assertSame('POST, OPTIONS', $response->headers->get('Allow'));
        foreach (['X-Private-Debug', 'Location', 'Set-Cookie'] as $name) self::assertFalse($response->headers->has($name));
        self::assertStringNotContainsString('private-marker', (string) $response);
        self::assertStringNotContainsString('private-marker', serialize($logger->records));
        self::assertSame(['request_id', 'status', 'error_code'], array_keys($logger->records[0][2]));
        self::assertSame($response->headers->get(RequestReference::HEADER), $logger->records[0][2]['request_id']);
        $bad = $responder->exception($request, new HttpException(429, headers: ['Retry-After' => 'private-marker', 'Allow' => ['GET']]));
        self::assertFalse($bad->headers->has('Retry-After'));
        self::assertFalse($bad->headers->has('Allow'));
    }

    public function testBrokenTemplateAndLoggerFallBackWithoutExposingTheirExceptions(): void
    {
        $logger = new class extends AbstractLogger {
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                throw new \RuntimeException('private-logger-marker');
            }
        };
        $responder = new HttpErrorResponder(new RequestReference(), new Environment(new ArrayLoader()), $logger);
        $response = $responder->exception(Request::create('/account/password'), new \RuntimeException('private-password-marker'));
        self::assertSame(500, $response->getStatusCode());
        self::assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        self::assertStringContainsString($response->headers->get(RequestReference::HEADER), $response->getContent());
        self::assertStringNotContainsString('private-', (string) $response);
        self::assertTrue($response->headers->hasCacheControlDirective('no-store'));
    }
}
