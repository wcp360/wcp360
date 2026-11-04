<?php

declare(strict_types=1);

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Wcp360\Core\Shared\Http\BoundarySubscriber;
use Wcp360\Core\Shared\Http\RequestReference;
use Wcp360\Core\Shared\Security\RuntimeContext;

// Disposable component harness, not the production application or its router.
if (!is_file('/.dockerenv') || !function_exists('frankenphp_handle_request')) {
    throw new RuntimeException('Disposable FrankenPHP worker required.');
}
require '/opt/wcp360/WCP360_Core/vendor/autoload.php';

$states = [];
foreach (['user', 'admin'] as $surface) {
    $references = new RequestReference();
    $boundary = new BoundarySubscriber(new RuntimeContext($surface, true, 'test', $surface.'.localhost'));
    $dispatcher = new EventDispatcher();
    $dispatcher->addSubscriber($boundary);
    $dispatcher->addListener(KernelEvents::REQUEST, static function (RequestEvent $event) use ($references): void {
        if ($event->isMainRequest()) {
            $references->begin($event->getRequest());
        }
    }, 64);
    $dispatcher->addListener(KernelEvents::EXCEPTION, static function (ExceptionEvent $event) use ($references): void {
        if (!$event->isMainRequest() || !$event->getThrowable() instanceof AccessDeniedHttpException) {
            return;
        }
        // BoundarySubscriber must issue the nonce before this error renderer runs.
        $event->setResponse(new JsonResponse([
            'nonce' => $event->getRequest()->attributes->get('_csp_nonce'),
            'reference' => $references->get($event->getRequest()),
            'subrequest_untouched' => null,
        ], 403));
    });
    $dispatcher->addListener(KernelEvents::RESPONSE, static function (ResponseEvent $event): void {
        $request = $event->getRequest();
        if ($event->isMainRequest() && $request->attributes->get('_probe_scenario') === 'forged') {
            $request->attributes->set('_csp_nonce', $request->attributes->get('_probe_forged_nonce'));
            $request->attributes->set('_wcp_candidate_request_reference', $request->attributes->get('_probe_forged_reference'));
        }
    });
    $dispatcher->addListener(KernelEvents::RESPONSE, static function (ResponseEvent $event) use ($references): void {
        if ($event->isMainRequest()) {
            $event->getResponse()->headers->set(RequestReference::HEADER, $references->get($event->getRequest()));
        }
    }, -256);
    $states[$surface] = [
        'kernel' => new HttpKernel($dispatcher, new ControllerResolver(), new RequestStack(), new ArgumentResolver()),
        'references' => $references,
    ];
}

$instance = bin2hex(random_bytes(16));
$sequence = 0;
$previousNonce = str_repeat('A', 24);
$previousReference = str_repeat('a', 32);

return static function (string $surface, string $scenario) use ($states, $instance, &$sequence, &$previousNonce, &$previousReference): array {
    if (!isset($states[$surface]) || !in_array($scenario, ['normal', 'forged', 'early-error', 'subrequest'], true)) {
        throw new InvalidArgumentException('Unknown component probe.');
    }
    ++$sequence;
    $kernel = $states[$surface]['kernel'];
    $references = $states[$surface]['references'];
    $host = $scenario === 'early-error' ? ($surface === 'user' ? 'admin' : 'user') : $surface;
    // Fixed logical hosts exercise the two unchanged RuntimeContext policies.
    // They do not qualify the outer Nginx production routing configuration.
    $request = Request::create('http://'.$host.'.localhost/probe');
    $request->headers->set(RequestReference::HEADER, $previousReference);
    $request->attributes->add([
        '_probe_scenario' => $scenario,
        '_probe_forged_nonce' => $previousNonce,
        '_probe_forged_reference' => $previousReference,
        '_csp_nonce' => $previousNonce,
        '_wcp_candidate_request_reference' => $previousReference,
        '_probe_cycle' => $request,
    ]);
    $earlyReference = $references->get($request);
    $request->attributes->set('_controller', static function (Request $main) use ($kernel, $references, $scenario): JsonResponse {
        $untouched = null;
        if ($scenario === 'subrequest') {
            $sub = Request::create('http://localhost/subrequest');
            $sub->attributes->set('_csp_nonce', 'subrequest-nonce-sentinel');
            $sub->attributes->set('_wcp_candidate_request_reference', 'subrequest-reference-sentinel');
            $sub->attributes->set('_controller', static fn (): Response => new Response('subrequest', 200, [
                'Content-Security-Policy' => 'subrequest-policy-sentinel',
                RequestReference::HEADER => 'subrequest-reference-sentinel',
            ]));
            $subResponse = $kernel->handle($sub, HttpKernelInterface::SUB_REQUEST);
            $untouched = $subResponse->getStatusCode() === 200
                && $subResponse->headers->get('Content-Security-Policy') === 'subrequest-policy-sentinel'
                && $subResponse->headers->get(RequestReference::HEADER) === 'subrequest-reference-sentinel'
                && $sub->attributes->get('_csp_nonce') === 'subrequest-nonce-sentinel'
                && $sub->attributes->get('_wcp_candidate_request_reference') === 'subrequest-reference-sentinel';
        }
        return new JsonResponse([
            'nonce' => $main->attributes->get('_csp_nonce'),
            'reference' => $references->get($main),
            'subrequest_untouched' => $untouched,
        ]);
    });
    $weak = WeakReference::create($request);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    $body = json_decode((string) $response->getContent(), true, 16, JSON_THROW_ON_ERROR);
    $headers = [];
    foreach (['Content-Security-Policy', RequestReference::HEADER, 'X-Frame-Options', 'Cache-Control', 'Strict-Transport-Security'] as $name) {
        $headers[$name] = $response->headers->get($name);
    }
    $result = [
        'instance' => $instance,
        'sequence' => $sequence,
        'surface' => $surface,
        'scenario' => $scenario,
        'status' => $response->getStatusCode(),
        'nonce' => $request->attributes->get('_csp_nonce'),
        'reference' => $references->get($request),
        'early_reference' => $earlyReference,
        'rendered_nonce' => $body['nonce'] ?? null,
        'rendered_reference' => $body['reference'] ?? null,
        'subrequest_untouched' => $body['subrequest_untouched'] ?? null,
        'forged_nonce' => $previousNonce,
        'forged_reference' => $previousReference,
        'headers' => $headers,
    ];
    $previousNonce = $result['nonce'];
    $previousReference = $result['reference'];
    unset($request, $response);
    gc_collect_cycles();
    // The kernels, dispatchers, subscribers and issuers all remain alive here.
    $result['collected'] = $weak->get() === null;
    return $result;
};
