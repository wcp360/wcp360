<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Wcp360\Core\Shared\Http\BoundarySubscriber;
use Wcp360\Core\Shared\Security\RuntimeContext;

final class BoundarySubscriberTest extends TestCase
{
    private function subscriber(): BoundarySubscriber
    {
        return new BoundarySubscriber(new RuntimeContext('user', true, 'test', 'user.localhost'));
    }

    public function testMainResponseHasCanonicalSecurityHeadersAndFreshNonce(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $request = Request::create('https://user.localhost/health');
        $request->attributes->set('_csp_nonce', 'forged-caller-nonce');
        $boundary->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        $nonce = $request->attributes->get('_csp_nonce');

        self::assertIsString($nonce);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/]{24}$/D', $nonce);
        self::assertSame(18, strlen((string) base64_decode($nonce, true)));
        self::assertNotSame('forged-caller-nonce', $nonce);

        $response = new Response();
        $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));
        $csp = $response->headers->get('Content-Security-Policy');
        self::assertIsString($csp);
        foreach ([
            "default-src 'none'", "base-uri 'none'", "object-src 'none'",
            "frame-ancestors 'none'", "form-action 'self'",
            "script-src 'self' 'nonce-".$nonce."'", "style-src 'self' 'nonce-".$nonce."'",
            "img-src 'self' data: blob:", "font-src 'self'",
            "connect-src 'self'", "frame-src 'none'",
        ] as $directive) {
            self::assertStringContainsString($directive, $csp);
        }
        self::assertStringNotContainsString("'unsafe-inline'", $csp);
        self::assertStringNotContainsString("'unsafe-eval'", $csp);
        foreach ([
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Strict-Transport-Security' => 'max-age=31536000',
            'X-WCP360-Status' => 'development-candidate',
        ] as $header => $expected) {
            self::assertSame($expected, $response->headers->get($header));
        }
        self::assertTrue($response->headers->hasCacheControlDirective('no-store'));
    }

    public function testWellFormedUnissuedNonceCannotControlEarlyErrorCsp(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('http://user.localhost/health');
        $unissued = str_repeat('A', 24);
        $request->attributes->set('_csp_nonce', $unissued);
        $response = new Response('early error', 500);

        // The request listener never ran, but the forged attribute looks valid.
        $this->subscriber()->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        $issued = $request->attributes->get('_csp_nonce');
        self::assertIsString($issued);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\\/]{24}$/D', $issued);
        self::assertNotSame($unissued, $issued);
        self::assertStringContainsString("'nonce-".$issued."'", (string) $response->headers->get('Content-Security-Policy'));
        self::assertStringNotContainsString("'nonce-".$unissued."'", (string) $response->headers->get('Content-Security-Policy'));
    }

    public function testDownstreamAttributeReplacementCannotOverrideIssuedMainNonce(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $request = Request::create('http://user.localhost/health');
        $boundary->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        $issued = $request->attributes->get('_csp_nonce');

        $request->attributes->set('_csp_nonce', str_repeat('B', 24));
        $response = new Response('ok');
        $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        self::assertSame($issued, $request->attributes->get('_csp_nonce'));
        self::assertStringContainsString("'nonce-".$issued."'", (string) $response->headers->get('Content-Security-Policy'));
        self::assertStringNotContainsString("'nonce-".str_repeat('B', 24)."'", (string) $response->headers->get('Content-Security-Policy'));
    }

    public function testDistinctEarlyErrorRequestsNeverReuseNonceInSameSubscriber(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $first = Request::create('http://user.localhost/early-a');
        $second = Request::create('http://user.localhost/early-b');

        foreach ([$first, $second] as $request) {
            $boundary->response(new ResponseEvent(
                $kernel, $request, HttpKernelInterface::MAIN_REQUEST, new Response('error', 500)
            ));
        }

        self::assertNotSame($first->attributes->get('_csp_nonce'), $second->attributes->get('_csp_nonce'));
    }

    public function testEarlyExceptionMakesTheNonceAvailableBeforeErrorTemplateRenders(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $request = Request::create('http://user.localhost/health');
        $request->attributes->set('_csp_nonce', str_repeat('X', 24));

        $events = BoundarySubscriber::getSubscribedEvents();
        self::assertSame(['exception', 256], $events['kernel.exception']);
        $boundary->exception(new ExceptionEvent(
            $kernel, $request, HttpKernelInterface::MAIN_REQUEST,
            new AccessDeniedHttpException('Early rejection.')
        ));

        $nonceBeforeRendering = $request->attributes->get('_csp_nonce');
        self::assertIsString($nonceBeforeRendering);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\\/]{24}$/D', $nonceBeforeRendering);
        self::assertNotSame(str_repeat('X', 24), $nonceBeforeRendering);

        // Simulate an error layout that reads the attribute before response().
        $response = new Response('<meta name="csp-nonce" content="'.$nonceBeforeRendering.'">', 403);
        $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));
        self::assertSame($nonceBeforeRendering, $request->attributes->get('_csp_nonce'));
        self::assertStringContainsString(
            "'nonce-".$nonceBeforeRendering."'",
            (string) $response->headers->get('Content-Security-Policy')
        );
    }

    public function testExceptionListenerRestoresIssuedNonceAndIgnoresSubrequests(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $main = Request::create('http://user.localhost/health');
        $boundary->request(new RequestEvent($kernel, $main, HttpKernelInterface::MAIN_REQUEST));
        $nonce = $main->attributes->get('_csp_nonce');
        $main->attributes->set('_csp_nonce', str_repeat('Y', 24));

        $boundary->exception(new ExceptionEvent(
            $kernel, $main, HttpKernelInterface::MAIN_REQUEST,
            new AccessDeniedHttpException('Denied.')
        ));
        self::assertSame($nonce, $main->attributes->get('_csp_nonce'));

        $sub = Request::create('http://user.localhost/subrequest');
        $boundary->exception(new ExceptionEvent(
            $kernel, $sub, HttpKernelInterface::SUB_REQUEST,
            new AccessDeniedHttpException('Subrequest denied.')
        ));
        self::assertFalse($sub->attributes->has('_csp_nonce'));
    }

    public function testDistinctMainRequestsDoNotReuseANonce(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $first = Request::create('http://user.localhost/health');
        $second = Request::create('http://user.localhost/health');

        $boundary->request(new RequestEvent($kernel, $first, HttpKernelInterface::MAIN_REQUEST));
        $boundary->request(new RequestEvent($kernel, $second, HttpKernelInterface::MAIN_REQUEST));
        self::assertNotSame($first->attributes->get('_csp_nonce'), $second->attributes->get('_csp_nonce'));
    }

    public function testEarlyErrorResponseGetsSafeNonceWithoutAdvertisingHstsOverHttp(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('http://user.localhost/health');
        $request->attributes->set('_csp_nonce', "bad\r\nnonce");
        $response = new Response('error', 500);

        $this->subscriber()->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        $nonce = $request->attributes->get('_csp_nonce');
        self::assertIsString($nonce);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/]{24}$/D', $nonce);
        self::assertStringContainsString("'nonce-".$nonce."'", (string) $response->headers->get('Content-Security-Policy'));
        self::assertStringNotContainsString('bad', (string) $response->headers->get('Content-Security-Policy'));
        self::assertFalse($response->headers->has('Strict-Transport-Security'));
        self::assertSame('DENY', $response->headers->get('X-Frame-Options'));
    }

    public function testSubrequestsCannotOverwriteMainResponsePolicy(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $main = Request::create('http://user.localhost/health');
        $sub = Request::create('http://user.localhost/fragment');

        $boundary->request(new RequestEvent($kernel, $main, HttpKernelInterface::MAIN_REQUEST));
        $nonce = $main->attributes->get('_csp_nonce');
        $boundary->request(new RequestEvent($kernel, $sub, HttpKernelInterface::SUB_REQUEST));
        self::assertFalse($sub->attributes->has('_csp_nonce'));

        $response = new Response(headers: ['Content-Security-Policy' => "default-src 'none'"]);
        $boundary->response(new ResponseEvent($kernel, $sub, HttpKernelInterface::SUB_REQUEST, $response));
        self::assertSame("default-src 'none'", $response->headers->get('Content-Security-Policy'));
        self::assertFalse($response->headers->has('X-Frame-Options'));
        self::assertSame($nonce, $main->attributes->get('_csp_nonce'));
    }


    public function testPlainHttpRemovesPreexistingDownstreamHstsHeader(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $request = Request::create('http://user.localhost/health');
        $response = new Response('ok', 200, [
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        ]);

        $boundary->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        self::assertFalse($response->headers->has('Strict-Transport-Security'));
        self::assertSame('DENY', $response->headers->get('X-Frame-Options'));
    }

    public function testEarlyHttpErrorResponseRemovesPreexistingHstsHeader(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('http://user.localhost/health');
        $response = new Response('error', 500, [
            'Strict-Transport-Security' => 'max-age=31536000',
        ]);

        // Some errors arrive before the request listener runs.
        $this->subscriber()->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        self::assertFalse($response->headers->has('Strict-Transport-Security'));
        $nonce = $request->attributes->get('_csp_nonce');
        self::assertIsString($nonce);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\\/]{24}$/D', $nonce);
        self::assertStringContainsString(
            "'nonce-".$nonce."'",
            (string) $response->headers->get('Content-Security-Policy')
        );
    }

    public function testHttpsOverridesDownstreamHstsWithCanonicalPolicy(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $boundary = $this->subscriber();
        $request = Request::create('https://user.localhost/health');
        $response = new Response('ok', 200, [
            'Strict-Transport-Security' => 'max-age=0',
        ]);

        $boundary->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        $boundary->response(new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response));

        self::assertSame('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
    }

    public function testWrongSurfaceHostIsRejectedBeforeNonceCreation(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('http://admin.localhost/health');
        try {
            $this->subscriber()->request(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
            self::fail('Wrong runtime host was admitted.');
        } catch (AccessDeniedHttpException) {
            self::assertFalse($request->attributes->has('_csp_nonce'));
        }
    }
}
