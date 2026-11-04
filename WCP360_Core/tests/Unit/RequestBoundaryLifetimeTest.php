<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Wcp360\Core\Shared\Http\BoundarySubscriber;
use Wcp360\Core\Shared\Http\RequestReference;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Issuer lifetime tests, not a substitute for a real FrankenPHP worker probe. */
final class RequestBoundaryLifetimeTest extends TestCase
{
    public function testCompletedRequestCanBeCollectedWhileBothIssuersRemainAlive(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = new BoundarySubscriber(new RuntimeContext('user', true, 'test', 'user.localhost'));
        $references = new RequestReference();
        $previousNonces = [];
        $previousReferences = [];

        for ($iteration = 0; $iteration < 32; ++$iteration) {
            $request = Request::create('https://user.localhost/health');
            $boundary->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
            $nonce = $request->attributes->get('_csp_nonce');
            self::assertIsString($nonce);
            $reference = $references->begin($request);
            self::assertNotContains($nonce, $previousNonces);
            self::assertNotContains($reference, $previousReferences);
            $previousNonces[] = $nonce;
            $previousReferences[] = $reference;

            $response = new Response();
            $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));
            self::assertStringContainsString("'nonce-".$nonce."'", (string) $response->headers->get('Content-Security-Policy'));
            self::assertSame($reference, $references->get($request));

            // Include a cycle, so this checks collection rather than only refcount release.
            $request->attributes->set('_test_request_cycle', $request);
            $weak = \WeakReference::create($request);
            unset($request);
            gc_collect_cycles();
            self::assertNull($weak->get(), 'Security issuers must not retain a completed Request.');
        }
    }

    public function testSharedIssuersDoNotAcceptAnotherLiveRequestsNonceOrReference(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = new BoundarySubscriber(new RuntimeContext('user', true, 'test', 'user.localhost'));
        $references = new RequestReference();
        $first = Request::create('https://user.localhost/first');
        $boundary->request(new RequestEvent($kernel, $first, HttpKernelInterface::MAIN_REQUEST));
        $firstNonce = $first->attributes->get('_csp_nonce');
        self::assertIsString($firstNonce);
        $firstReference = $references->begin($first);

        $second = Request::create('https://user.localhost/second');
        $second->attributes->set('_csp_nonce', $firstNonce);
        $second->attributes->set('_wcp_candidate_request_reference', $firstReference);
        $boundary->request(new RequestEvent($kernel, $second, HttpKernelInterface::MAIN_REQUEST));
        $secondNonce = $second->attributes->get('_csp_nonce');
        self::assertIsString($secondNonce);
        $secondReference = $references->get($second);
        self::assertNotSame($firstNonce, $secondNonce);
        self::assertNotSame($firstReference, $secondReference);

        // Finish the older request after the newer one has tried to replace its values.
        $first->attributes->set('_csp_nonce', $secondNonce);
        $first->attributes->set('_wcp_candidate_request_reference', $secondReference);
        $response = new Response();
        $boundary->response(new ResponseEvent($kernel, $first, HttpKernelInterface::MAIN_REQUEST, $response));
        self::assertSame($firstNonce, $first->attributes->get('_csp_nonce'));
        self::assertSame($firstReference, $references->get($first));
        $csp = (string) $response->headers->get('Content-Security-Policy');
        self::assertStringContainsString("'nonce-".$firstNonce."'", $csp);
        self::assertStringNotContainsString("'nonce-".$secondNonce."'", $csp);
        self::assertSame($secondReference, $references->get($second));
    }
}
