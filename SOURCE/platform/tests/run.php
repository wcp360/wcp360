<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Wcp360\Application\ActionHandler;
use Wcp360\Application\ActionRegistry;
use Wcp360\Application\PlatformKernel;
use Wcp360\Security\AuthenticationService;
use Wcp360\Database\DatabaseCatalog;
use Wcp360\Application\JobSubmissionService;
use Wcp360\Ops\IdempotencyRecord;
use Wcp360\Ops\IdempotencyScope;
use Wcp360\Persistence\DurableJobStore;
use Wcp360\Security\SessionSurfacePolicy;
use Wcp360\Transport\ApiDispatcher;
use Wcp360\Transport\ApiRequestEnvelope;
use Wcp360\Transport\CsrfGuard;
use Wcp360\Transport\IdempotencyGuard;
use Wcp360\Transport\RateLimitGuard;
use Wcp360\Transport\RequestPolicyService;
use Wcp360\Transport\RetryPolicy;
use Wcp360\Transport\SynchronizerCsrfGuard;
use Wcp360\Transport\ExpectedOriginResolver;
use Wcp360\Transport\SessionCsrfTokenVerifier;
use Wcp360\Security\SecurityHeadersPolicy;
use Wcp360\Transport\RetryProfile;
use Wcp360\Contracts\ActionCatalog;
use Wcp360\Contracts\ApiContractDefinition;
use Wcp360\Contracts\ApiContractRegistry;
use Wcp360\Contracts\OperationKind;
use Wcp360\Contracts\PermissionDefinition;
use Wcp360\Contracts\PermissionRegistry;
use Wcp360\Contracts\RequestSchema;
use Wcp360\Ops\AuditRecord;
use Wcp360\Ops\JobRecord;
use Wcp360\Observability\StructuredLogRecord;
use Wcp360\Security\AuthorizationContext;
use Wcp360\Security\AuthorizationDecision;
use Wcp360\Security\AuthorizationRequest;
use Wcp360\Security\AuthorizationService;
use Wcp360\Security\PolicyGuard;
use Wcp360\Security\ScopeGuard;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;
use Wcp360\Shared\Uuid;

$tests = [];
$test = static function (string $name, callable $fn) use (&$tests): void {
    $tests[$name] = $fn;
};
$assert = static function (bool $condition, string $message = 'assertion failed'): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};
$expectViolation = static function (callable $fn) use ($assert): void {
    try {
        $fn();
    } catch (ContractViolation) {
        return;
    }
    $assert(false, 'expected ContractViolation');
};

$perm = static fn(string $id, string $surface): PermissionDefinition => PermissionDefinition::fromArray([
    'id' => $id,
    'surface' => $surface,
    'status' => 'ACTIVE',
]);

$contract = static fn(): ApiContractDefinition => ApiContractDefinition::fromArray([
    'contract_id' => 'API-WCP-0101',
    'version' => '1.0',
    'surface' => 'WCP-USER',
    'authentication' => 'AUTH-U',
    'permission_id' => 'applications.read',
    'action_id' => 'applications.read',
    'resource_type' => 'application',
    'tenant_scope' => 'TS-T',
    'request_schema' => '{tenant_id,application_id?,cursor?,limit?}',
    'response_schema' => 'ApplicationView/Collection',
    'errors' => 'E-R',
    'timeout' => '10s',
    'retry' => 'RR',
    'idempotency' => 'none',
    'audit' => 'AU0',
    'security' => 'S-T',
    'compatibility' => 'C1/D0',
    'test_id' => 'TEST-API-0101',
]);

$allowScope = new class implements ScopeGuard {
    public function allows(AuthorizationContext $context, string $resourceType, ?string $resourceId): bool
    {
        return $context->tenantId !== null && $resourceType !== '';
    }
};
$allowPolicy = new class implements PolicyGuard {
    public function evaluate(AuthorizationContext $context, AuthorizationRequest $request): AuthorizationDecision
    {
        if ($context->surface === Surface::Admin && !$context->mfaSatisfied) {
            return AuthorizationDecision::deny(CanonicalErrorCode::MfaRequired, 'admin MFA required');
        }
        return AuthorizationDecision::allow();
    }
};

$test('canonical error registry contains 23 unique codes', static function () use ($assert): void {
    $values = array_map(static fn(CanonicalErrorCode $c): string => $c->value, CanonicalErrorCode::cases());
    $assert(count($values) === 23);
    $assert(count(array_unique($values)) === 23);
    $assert(in_array('CONCURRENCY_CONFLICT', $values, true));
    $assert(!in_array('CONCURRENT_MODIFICATION', $values, true));
});

$test('permission surfaces stay isolated', static function () use ($assert, $perm): void {
    $user = $perm('applications.read', 'WCP-USER');
    $admin = $perm('servers.read', 'WCP-ADMIN');
    $both = $perm('monitoring.read', 'BOTH / scope-bound');
    $assert($user->allowsSurface(Surface::User));
    $assert(!$user->allowsSurface(Surface::Admin));
    $assert($admin->allowsSurface(Surface::Admin));
    $assert(!$admin->allowsSurface(Surface::User));
    $assert($both->allowsSurface(Surface::User) && $both->allowsSurface(Surface::Admin));
});

$test('request schema parses required optional and array fields', static function () use ($assert): void {
    [$required, $optional] = RequestSchema::parse('{domain_id,type,name,ttl,values[],priority?,idempotency_key}');
    $assert($required === ['domain_id', 'type', 'name', 'ttl', 'values', 'idempotency_key']);
    $assert($optional === ['priority']);
});

$test('request schema rejects unknown fields', static function () use ($contract, $expectViolation): void {
    $definition = $contract();
    $expectViolation(static fn() => RequestSchema::validateFieldNames($definition, [
        'tenant_id' => 'x',
        'unexpected' => true,
    ]));
});

$test('authorization fails closed on unknown permission', static function () use ($assert, $allowScope, $allowPolicy): void {
    $service = new AuthorizationService(new PermissionRegistry([]), $allowScope, $allowPolicy);
    $context = new AuthorizationContext(
        Surface::User,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['ghost.permission'],
        false,
        false,
    );
    $decision = $service->decide($context, new AuthorizationRequest('ghost.permission', 'ghost.action', 'application', null));
    $assert(!$decision->allowed);
    $assert($decision->errorCode === CanonicalErrorCode::AuthorizationDenied);
});

$test('authorization rejects cross-surface permission', static function () use ($assert, $perm, $allowScope, $allowPolicy): void {
    $service = new AuthorizationService(new PermissionRegistry([$perm('applications.read', 'WCP-USER')]), $allowScope, $allowPolicy);
    $context = new AuthorizationContext(
        Surface::Admin,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['applications.read'],
        true,
        false,
    );
    $decision = $service->decide($context, new AuthorizationRequest('applications.read', 'applications.read', 'application', null));
    $assert(!$decision->allowed);
});

$test('kernel requires explicit registered handler', static function () use ($assert, $perm, $contract, $allowScope, $allowPolicy): void {
    $permissions = new PermissionRegistry([$perm('applications.read', 'WCP-USER')]);
    $service = new AuthorizationService($permissions, $allowScope, $allowPolicy);
    $kernel = new PlatformKernel(new ApiContractRegistry([$contract()]), new AuthenticationService(), $service, new ActionRegistry(ActionCatalog::fromApiContracts(new ApiContractRegistry([$contract()]))));
    $context = new AuthorizationContext(
        Surface::User,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['applications.read'],
        false,
        false,
    );
    $result = $kernel->execute('API-WCP-0101', ['tenant_id' => '22222222-2222-4222-8222-222222222222'], $context);
    $assert(!$result->ok && $result->errorCode === CanonicalErrorCode::ServiceUnavailable);
});

$test('kernel executes only exact registered action after authorization', static function () use ($assert, $perm, $contract, $allowScope, $allowPolicy): void {
    $handler = new class implements ActionHandler {
        public function actionId(): string { return 'applications.read'; }
        public function handle(array $input, AuthorizationContext $context): array
        {
            return ['items' => [], 'tenant_id' => $input['tenant_id']];
        }
    };
    $permissions = new PermissionRegistry([$perm('applications.read', 'WCP-USER')]);
    $service = new AuthorizationService($permissions, $allowScope, $allowPolicy);
    $kernel = new PlatformKernel(new ApiContractRegistry([$contract()]), new AuthenticationService(), $service, new ActionRegistry(ActionCatalog::fromApiContracts(new ApiContractRegistry([$contract()])), [$handler]));
    $context = new AuthorizationContext(
        Surface::User,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['applications.read'],
        false,
        false,
    );
    $result = $kernel->execute('API-WCP-0101', ['tenant_id' => '22222222-2222-4222-8222-222222222222'], $context);
    $assert($result->ok);
    $assert($result->data['items'] === []);
});

$test('job record enforces attempt and revision invariants', static function () use ($expectViolation): void {
    $expectViolation(static fn() => new JobRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'),
        null,
        'example.job',
        'example.action',
        null,
        null,
        'QUEUED',
        null,
        null,
        2,
        1,
        new DateTimeImmutable(),
        null,
        null,
        null,
        new Uuid('22222222-2222-4222-8222-222222222222'),
        1,
    ));
});

$test('audit metadata rejects obvious secret-bearing keys', static function () use ($expectViolation): void {
    $expectViolation(static fn() => new AuditRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'),
        'example.event',
        null,
        null,
        Surface::Admin,
        'example.action',
        'server',
        null,
        new Uuid('22222222-2222-4222-8222-222222222222'),
        new DateTimeImmutable(),
        ['token' => 'secret'],
    ));
});


$test('API contract surface denies cross-surface execution even for BOTH permission', static function () use ($assert, $perm, $allowScope, $allowPolicy): void {
    $definition = ApiContractDefinition::fromArray([
        'contract_id' => 'API-WCP-1006',
        'version' => '1.0',
        'surface' => 'WCP-USER',
        'authentication' => 'AUTH-U',
        'permission_id' => 'monitoring.read',
        'action_id' => 'monitoring.read',
        'resource_type' => 'monitoring',
        'tenant_scope' => 'TS-B',
        'request_schema' => '{tenant_id}',
        'response_schema' => 'MonitoringView',
        'errors' => 'E-R',
        'timeout' => '10s',
        'retry' => 'RR',
        'idempotency' => 'none',
        'audit' => 'AU0',
        'security' => 'S-Q',
        'compatibility' => 'C1/D0',
        'test_id' => 'TEST-API-1006',
    ]);
    $handler = new class implements ActionHandler {
        public function actionId(): string { return 'monitoring.read'; }
        public function handle(array $input, AuthorizationContext $context): array { return ['unexpected' => true]; }
    };
    $service = new AuthorizationService(
        new PermissionRegistry([$perm('monitoring.read', 'BOTH / scope-bound')]),
        $allowScope,
        $allowPolicy,
    );
    $kernel = new PlatformKernel(new ApiContractRegistry([$definition]), new AuthenticationService(), $service, new ActionRegistry(ActionCatalog::fromApiContracts(new ApiContractRegistry([$definition])), [$handler]));
    $context = new AuthorizationContext(
        Surface::Admin,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['monitoring.read'],
        true,
        false,
    );
    $result = $kernel->execute('API-WCP-1006', ['tenant_id' => '22222222-2222-4222-8222-222222222222'], $context);
    $assert(!$result->ok && $result->errorCode === CanonicalErrorCode::AuthorizationDenied);
});

$test('session surface policy fixes distinct USER and ADMIN cookies', static function () use ($assert, $expectViolation): void {
    $path = tempnam(sys_get_temp_dir(), 'wcp-session-');
    if ($path === false) { throw new RuntimeException('temp file failure'); }
    file_put_contents($path, json_encode([
        'version' => 'v1.0',
        'invariant' => 'WCP-USER session != WCP-ADMIN session',
        'cookie_user' => '__Host-wcp_user_session',
        'cookie_admin' => '__Host-wcp_admin_session',
        'required_cookie_attributes' => ['Secure','HttpOnly','SameSite=Strict','Path=/','host-only','no Domain attribute'],
        'policies' => [
            ['policy'=>'cookie','wcp_user'=>'__Host-wcp_user_session','wcp_admin'=>'__Host-wcp_admin_session'],
            ['policy'=>'idle timeout','wcp_user'=>'30 min','wcp_admin'=>'15 min'],
            ['policy'=>'absolute lifetime','wcp_user'=>'12 h','wcp_admin'=>'8 h'],
            ['policy'=>'remember-me','wcp_user'=>'disabled baseline','wcp_admin'=>'forbidden'],
            ['policy'=>'MFA at login','wcp_user'=>'policy/account dependent','wcp_admin'=>'mandatory'],
            ['policy'=>'fresh MFA high-risk','wcp_user'=>'<=10 min when required','wcp_admin'=>'<=5 min'],
            ['policy'=>'privileged child context','wcp_user'=>'unavailable','wcp_admin'=>'idle 5 min / absolute 15 min'],
            ['policy'=>'break-glass','wcp_user'=>'unavailable','wcp_admin'=>'max 15 min, no auto-extension'],
        ],
    ], JSON_THROW_ON_ERROR));
    try {
        $policy = SessionSurfacePolicy::fromJsonFile($path);
        $assert($policy->cookieName(Surface::User) === '__Host-wcp_user_session');
        $assert($policy->cookieName(Surface::Admin) === '__Host-wcp_admin_session');
        $assert($policy->policy('MFA at login', Surface::Admin) === 'mandatory');
        $expectViolation(static fn() => $policy->assertCookieName(Surface::Admin, '__Host-wcp_user_session'));
        $expectViolation(static fn() => $policy->cookieName(Surface::Policy));
    } finally {
        @unlink($path);
    }
});

$test('idempotency scope CHECK and fingerprint conflict are fail-closed', static function () use ($assert, $expectViolation): void {
    $tenant = new Uuid('22222222-2222-4222-8222-222222222222');
    $record = new IdempotencyRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'),
        IdempotencyScope::Tenant,
        null,
        $tenant,
        'applications.create',
        'opaque-key',
        'sha256:abc',
        'ACQUIRED',
        null,
        new DateTimeImmutable('2026-10-29T09:00:00Z'),
        new DateTimeImmutable('2026-10-30T09:00:00Z'),
        1,
    );
    $assert(!$record->conflictsWithFingerprint('sha256:abc'));
    $assert($record->conflictsWithFingerprint('sha256:different'));
    $expectViolation(static fn() => new IdempotencyRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'),
        IdempotencyScope::Tenant,
        new Uuid('33333333-3333-4333-8333-333333333333'),
        $tenant,
        'applications.create', 'key', 'fingerprint', 'ACQUIRED', null,
        new DateTimeImmutable('2026-10-29T09:00:00Z'),
        new DateTimeImmutable('2026-10-30T09:00:00Z'),
        1,
    ));
});

$test('audit metadata rejects nested secret-bearing keys', static function () use ($expectViolation): void {
    $expectViolation(static fn() => new AuditRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'),
        'example.event', null, null, Surface::Admin, 'example.action', 'server', null,
        new Uuid('22222222-2222-4222-8222-222222222222'),
        new DateTimeImmutable(),
        ['safe' => ['authorization' => 'Bearer secret']],
    ));
});

$test('durable job submission enforces correlation and tenant before store', static function () use ($assert, $expectViolation): void {
    $store = new class implements DurableJobStore {
        public bool $called = false;
        public function enqueue(JobRecord $job, AuditRecord $audit, ?IdempotencyRecord $idempotency): void { $this->called = true; }
    };
    $tenant = new Uuid('22222222-2222-4222-8222-222222222222');
    $correlation = new Uuid('33333333-3333-4333-8333-333333333333');
    $job = new JobRecord(
        new Uuid('11111111-1111-4111-8111-111111111111'), $tenant,
        'applications.create', 'applications.create', null, null, 'QUEUED', null, null,
        0, 3, new DateTimeImmutable(), null, null, null, $correlation, 1,
    );
    $audit = new AuditRecord(
        new Uuid('44444444-4444-4444-8444-444444444444'), 'application.create.requested',
        $tenant, null, Surface::User, 'applications.create', 'application', null,
        $correlation, new DateTimeImmutable(), [],
    );
    $service = new JobSubmissionService($store);
    $service->submit($job, $audit);
    $assert($store->called);

    $wrongAudit = new AuditRecord(
        new Uuid('55555555-5555-4555-8555-555555555555'), 'application.create.requested',
        $tenant, null, Surface::User, 'applications.create', 'application', null,
        new Uuid('66666666-6666-4666-8666-666666666666'), new DateTimeImmutable(), [],
    );
    $expectViolation(static fn() => $service->submit($job, $wrongAudit));
});


$test('EMERGENCY is a distinct contract surface with no browser session cookie', static function () use ($assert, $perm, $expectViolation): void {
    $definition = ApiContractDefinition::fromArray([
        'contract_id' => 'API-WCP-0708', 'version' => '1.0', 'surface' => 'EMERGENCY',
        'authentication' => 'AUTH-E', 'permission_id' => 'break_glass.use',
        'action_id' => 'break_glass.start', 'resource_type' => 'break_glass_session',
        'tenant_scope' => 'TS-P', 'request_schema' => '{reason,idempotency_key}',
        'response_schema' => 'BreakGlassSessionView', 'errors' => 'E-P', 'timeout' => '15s',
        'retry' => 'RN', 'idempotency' => 'key-required', 'audit' => 'AU5',
        'security' => 'S-X', 'compatibility' => 'C1/D0', 'test_id' => 'TEST-API-0708',
    ]);
    $assert($definition->allowsSurface(Surface::Emergency));
    $assert(!$definition->allowsSurface(Surface::Admin));
    $permission = $perm('break_glass.use', 'POLICY / privileged');
    $assert($permission->allowsSurface(Surface::Emergency));

    $path = tempnam(sys_get_temp_dir(), 'wcp-session-');
    if ($path === false) { throw new RuntimeException('temp file failure'); }
    file_put_contents($path, json_encode([
        'version'=>'v1.0','invariant'=>'WCP-USER session != WCP-ADMIN session',
        'cookie_user'=>'__Host-wcp_user_session','cookie_admin'=>'__Host-wcp_admin_session',
        'required_cookie_attributes'=>['Secure','HttpOnly','SameSite=Strict','Path=/','host-only','no Domain attribute'],
        'policies'=>[
            ['policy'=>'cookie','wcp_user'=>'__Host-wcp_user_session','wcp_admin'=>'__Host-wcp_admin_session'],
            ['policy'=>'idle timeout','wcp_user'=>'30 min','wcp_admin'=>'15 min'],
            ['policy'=>'absolute lifetime','wcp_user'=>'12 h','wcp_admin'=>'8 h'],
            ['policy'=>'remember-me','wcp_user'=>'disabled baseline','wcp_admin'=>'forbidden'],
            ['policy'=>'MFA at login','wcp_user'=>'policy/account dependent','wcp_admin'=>'mandatory'],
            ['policy'=>'fresh MFA high-risk','wcp_user'=>'<=10 min when required','wcp_admin'=>'<=5 min'],
            ['policy'=>'privileged child context','wcp_user'=>'unavailable','wcp_admin'=>'idle 5 min / absolute 15 min'],
            ['policy'=>'break-glass','wcp_user'=>'unavailable','wcp_admin'=>'max 15 min, no auto-extension'],
        ],
    ], JSON_THROW_ON_ERROR));
    try {
        $policy = SessionSurfacePolicy::fromJsonFile($path);
        $expectViolation(static fn() => $policy->cookieName(Surface::Emergency));
    } finally { @unlink($path); }
});


$test('authentication profiles enforce USER ADMIN POLICY PRIV and EMERGENCY semantics', static function () use ($assert): void {
    $service = new AuthenticationService();
    $mk = static fn(string $surface, string $auth): ApiContractDefinition => ApiContractDefinition::fromArray([
        'contract_id'=>'API-WCP-0999','version'=>'1.0','surface'=>$surface,'authentication'=>$auth,
        'permission_id'=>'example.read','action_id'=>'example.read','resource_type'=>'example','tenant_scope'=>'TS-G',
        'request_schema'=>'{}','response_schema'=>'Example','errors'=>'E-R','timeout'=>'10s','retry'=>'RR',
        'idempotency'=>'none','audit'=>'AU0','security'=>'S-Q','compatibility'=>'C1/D0','test_id'=>'TEST-API-0999',
    ]);
    $principal = new Uuid('11111111-1111-4111-8111-111111111111');
    $admin = new AuthorizationContext(Surface::Admin, $principal, null, [], true, true, true, false, true);
    $assert($service->decide($mk('WCP-ADMIN','AUTH-PRIV'), $admin)->allowed);
    $adminNoReauth = new AuthorizationContext(Surface::Admin, $principal, null, [], true, true);
    $d = $service->decide($mk('WCP-ADMIN','AUTH-PRIV'), $adminNoReauth);
    $assert(!$d->allowed && $d->errorCode === CanonicalErrorCode::ReauthenticationRequired);

    $policy = new AuthorizationContext(Surface::Policy, $principal, null, [], false, false, true, true);
    $assert($service->decide($mk('POLICY','AUTH-P'), $policy)->allowed);

    $emergency = new AuthorizationContext(
        Surface::Emergency, $principal, null, ['break_glass.use'], true, true,
        true, false, true, true, true, new DateTimeImmutable('2035-01-01T00:00:00Z'),
    );
    $assert($service->decide($mk('EMERGENCY','AUTH-E'), $emergency)->allowed);
    $expired = new AuthorizationContext(
        Surface::Emergency, $principal, null, ['break_glass.use'], true, true,
        true, false, true, true, true, new DateTimeImmutable('2020-01-01T00:00:00Z'),
    );
    $assert(!$service->decide($mk('EMERGENCY','AUTH-E'), $expired)->allowed);
});

$test('kernel rejects unauthenticated context before authorization/handler', static function () use ($assert, $perm, $contract, $allowScope, $allowPolicy): void {
    $handler = new class implements ActionHandler {
        public bool $called = false;
        public function actionId(): string { return 'applications.read'; }
        public function handle(array $input, AuthorizationContext $context): array { $this->called = true; return []; }
    };
    $authz = new AuthorizationService(new PermissionRegistry([$perm('applications.read','WCP-USER')]), $allowScope, $allowPolicy);
    $kernel = new PlatformKernel(new ApiContractRegistry([$contract()]), new AuthenticationService(), $authz, new ActionRegistry(ActionCatalog::fromApiContracts(new ApiContractRegistry([$contract()])), [$handler]));
    $context = new AuthorizationContext(
        Surface::User, new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'), ['applications.read'], false, false, false,
    );
    $result = $kernel->execute('API-WCP-0101', ['tenant_id'=>'22222222-2222-4222-8222-222222222222'], $context);
    $assert(!$result->ok && $result->errorCode === CanonicalErrorCode::AuthenticationRequired);
    $assert(!$handler->called);
});


$test('structured logs are correlated and recursively secret-safe', static function () use ($assert, $expectViolation): void {
    $record = new StructuredLogRecord(
        new DateTimeImmutable('2026-10-29T09:00:00Z'), 'INFO', 'platform', 'request.accepted',
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'), null,
        new Uuid('33333333-3333-4333-8333-333333333333'),
        ['route' => 'API-WCP-0101'],
    );
    $data = $record->toArray();
    $assert($data['correlation_id'] === '11111111-1111-4111-8111-111111111111');
    $expectViolation(static fn() => new StructuredLogRecord(
        new DateTimeImmutable(), 'INFO', 'platform', 'bad',
        new Uuid('11111111-1111-4111-8111-111111111111'), null, null, null,
        ['nested' => ['client-secret' => 'must-not-log']],
    ));
});


$test('transport policy enforces rate CSRF and idempotency before kernel', static function () use ($assert, $perm, $allowScope, $allowPolicy): void {
    $definition = ApiContractDefinition::fromArray([
        'contract_id'=>'API-WCP-0110','version'=>'1.0','surface'=>'WCP-USER','authentication'=>'AUTH-U',
        'permission_id'=>'applications.create','action_id'=>'applications.create','resource_type'=>'application','tenant_scope'=>'TS-T',
        'request_schema'=>'{tenant_id,idempotency_key}','response_schema'=>'JobAccepted','errors'=>'E-M','timeout'=>'10s accept',
        'retry'=>'RK','idempotency'=>'key-required','audit'=>'AU3','security'=>'S-T','compatibility'=>'C1/D0','test_id'=>'TEST-API-0110',
    ]);
    $handler = new class implements ActionHandler {
        public bool $called = false;
        public function actionId(): string { return 'applications.create'; }
        public function handle(array $input, AuthorizationContext $context): array { $this->called=true; return ['accepted'=>true]; }
    };
    $authz = new AuthorizationService(new PermissionRegistry([$perm('applications.create','WCP-USER')]), $allowScope, $allowPolicy);
    $contracts = new ApiContractRegistry([$definition]);
    $kernel = new PlatformKernel($contracts, new AuthenticationService(), $authz, new ActionRegistry(ActionCatalog::fromApiContracts($contracts), [$handler]));
    $csrf = new class implements CsrfGuard { public bool $allow=false; public function allows(ApiRequestEnvelope $r, ApiContractDefinition $c): bool { return $this->allow; } };
    $rate = new class implements RateLimitGuard { public bool $allow=true; public function allows(ApiRequestEnvelope $r, ApiContractDefinition $c): bool { return $this->allow; } };
    $idem = new class implements IdempotencyGuard { public bool $allow=true; public function allows(ApiRequestEnvelope $r, ApiContractDefinition $c): bool { return $this->allow; } };
    $dispatcher = new ApiDispatcher($contracts, new RequestPolicyService($csrf,$rate,$idem), $kernel);
    $context = new AuthorizationContext(
        Surface::User,new Uuid('11111111-1111-4111-8111-111111111111'),new Uuid('22222222-2222-4222-8222-222222222222'),
        ['applications.create'],false,false,
    );
    $mk = static fn(?string $key): ApiRequestEnvelope => new ApiRequestEnvelope(
        'API-WCP-0110',['tenant_id'=>'22222222-2222-4222-8222-222222222222','idempotency_key'=>'payload-key'],
        $context,new Uuid('33333333-3333-4333-8333-333333333333'),new Uuid('44444444-4444-4444-8444-444444444444'),OperationKind::Mutation,true,$key,
    );
    $response=$dispatcher->dispatch($mk('header-key'));
    $assert(!$response->ok && $response->errorCode===CanonicalErrorCode::AuthorizationDenied && !$handler->called);
    $csrf->allow=true;
    $response=$dispatcher->dispatch($mk(null));
    $assert(!$response->ok && $response->errorCode===CanonicalErrorCode::ValidationFailed && !$handler->called);
    $idem->allow=false;
    $response=$dispatcher->dispatch($mk('header-key'));
    $assert(!$response->ok && $response->errorCode===CanonicalErrorCode::IdempotencyConflict && !$handler->called);
    $idem->allow=true; $rate->allow=false;
    $response=$dispatcher->dispatch($mk('header-key'));
    $assert(!$response->ok && $response->errorCode===CanonicalErrorCode::RateLimited && !$handler->called);
    $rate->allow=true;
    $response=$dispatcher->dispatch($mk('header-key'));
    $assert($response->ok && $handler->called);
    $array=$response->toArray();
    $assert($array['request_id']==='33333333-3333-4333-8333-333333333333');
});

$test('retry policy permits only bounded RR transport retry and reconciles RN unknown outcome', static function () use ($assert): void {
    $assert(RetryPolicy::mayRetryTransport(RetryProfile::Read,0,false,null));
    $assert(RetryPolicy::mayRetryTransport(RetryProfile::Read,1,true,CanonicalErrorCode::RateLimited));
    $assert(!RetryPolicy::mayRetryTransport(RetryProfile::Read,2,false,null));
    $assert(!RetryPolicy::mayRetryTransport(RetryProfile::KeyProtected,0,false,null));
    $assert(!RetryPolicy::mayRetryTransport(RetryProfile::NoBlindRetry,0,false,null));
    $assert(RetryPolicy::requiresReconciliationBeforeExecutionRetry(RetryProfile::NoBlindRetry,CanonicalErrorCode::UnknownOutcome));
    $assert(!RetryPolicy::requiresReconciliationBeforeExecutionRetry(RetryProfile::NoBlindRetry,CanonicalErrorCode::DependencyFailure));
});


$test('transport operation kind is constrained by canonical retry/idempotency profile', static function () use ($assert): void {
    $base = [
        'contract_id'=>'API-WCP-9001','version'=>'1.0','surface'=>'WCP-USER','authentication'=>'AUTH-U',
        'permission_id'=>'applications.read','action_id'=>'applications.read','resource_type'=>'application','tenant_scope'=>'TS-T',
        'request_schema'=>'{tenant_id}','response_schema'=>'View','errors'=>'E-R','timeout'=>'10s',
        'audit'=>'AU0','security'=>'S-T','compatibility'=>'C1/D0','test_id'=>'TEST-API-9001',
    ];
    $read = ApiContractDefinition::fromArray($base + ['retry'=>'RR','idempotency'=>'none']);
    $assert($read->allowsOperationKind(OperationKind::Read));
    $assert(!$read->allowsOperationKind(OperationKind::Mutation));
    $assert(!$read->requiresIdempotencyKey(OperationKind::Read));

    $mutation = ApiContractDefinition::fromArray(array_replace($base, [
        'contract_id'=>'API-WCP-9002','action_id'=>'applications.create','permission_id'=>'applications.create',
        'retry'=>'RK','idempotency'=>'key-required','test_id'=>'TEST-API-9002',
    ]));
    $assert(!$mutation->allowsOperationKind(OperationKind::Read));
    $assert($mutation->allowsOperationKind(OperationKind::Mutation));
    $assert($mutation->requiresIdempotencyKey(OperationKind::Mutation));

    $mixed = ApiContractDefinition::fromArray(array_replace($base, [
        'contract_id'=>'API-WCP-9003','action_id'=>'security.sessions.manage','permission_id'=>'security.sessions.manage',
        'resource_type'=>'session','retry'=>'RK','idempotency'=>'key when mutating','test_id'=>'TEST-API-9003',
    ]));
    $assert($mixed->allowsOperationKind(OperationKind::Read));
    $assert($mixed->allowsOperationKind(OperationKind::Mutation));
    $assert(!$mixed->requiresIdempotencyKey(OperationKind::Read));
    $assert($mixed->requiresIdempotencyKey(OperationKind::Mutation));
});

$test('synchronizer CSRF requires exact origin and session-bound token', static function () use ($assert): void {
    $originResolver = new class implements ExpectedOriginResolver {
        public function expectedOrigin(Surface $surface): string
        {
            return match ($surface) {
                Surface::User => 'https://user.wcp360.invalid',
                Surface::Admin => 'https://admin.wcp360.invalid',
                default => 'https://policy.wcp360.invalid',
            };
        }
    };
    $tokenVerifier = new class implements SessionCsrfTokenVerifier {
        public function verify(AuthorizationContext $authorization, string $presentedToken): bool
        {
            return hash_equals('session-bound-token-1234567890', $presentedToken);
        }
    };
    $guard = new SynchronizerCsrfGuard($originResolver, $tokenVerifier);
    $context = new AuthorizationContext(
        Surface::User,
        new Uuid('11111111-1111-4111-8111-111111111111'),
        new Uuid('22222222-2222-4222-8222-222222222222'),
        ['applications.create'], false, false,
    );
    $contract = ApiContractDefinition::fromArray([
        'contract_id'=>'API-WCP-9010','version'=>'1.0','surface'=>'WCP-USER','authentication'=>'AUTH-U',
        'permission_id'=>'applications.create','action_id'=>'applications.create','resource_type'=>'application','tenant_scope'=>'TS-T',
        'request_schema'=>'{tenant_id,idempotency_key}','response_schema'=>'JobAccepted','errors'=>'E-M','timeout'=>'10s accept',
        'retry'=>'RK','idempotency'=>'key-required','audit'=>'AU3','security'=>'S-T','compatibility'=>'C1/D0','test_id'=>'TEST-API-9010',
    ]);
    $request = new ApiRequestEnvelope(
        'API-WCP-9010', ['tenant_id'=>'22222222-2222-4222-8222-222222222222','idempotency_key'=>'key'], $context,
        new Uuid('33333333-3333-4333-8333-333333333333'), new Uuid('44444444-4444-4444-8444-444444444444'),
        OperationKind::Mutation, true, 'key', 'https://user.wcp360.invalid', 'session-bound-token-1234567890',
    );
    $assert($guard->allows($request, $contract));
    $badOrigin = new ApiRequestEnvelope(
        $request->contractId, $request->payload, $context, $request->requestId, $request->correlationId,
        OperationKind::Mutation, true, 'key', 'https://admin.wcp360.invalid', 'session-bound-token-1234567890',
    );
    $assert(!$guard->allows($badOrigin, $contract));
    $badToken = new ApiRequestEnvelope(
        $request->contractId, $request->payload, $context, $request->requestId, $request->correlationId,
        OperationKind::Mutation, true, 'key', 'https://user.wcp360.invalid', 'wrong-token-1234567890',
    );
    $assert(!$guard->allows($badToken, $contract));
    $assert(SynchronizerCsrfGuard::AJAX_HEADER === 'X-WCP-CSRF');
});

$test('security headers implement canonical CSP and browser isolation baseline', static function () use ($assert, $expectViolation): void {
    $headers = SecurityHeadersPolicy::headers('AbCdEfGhIjKlMnOpQrStUvWxYz012345', ['wss://admin.wcp360.invalid']);
    $csp = $headers['Content-Security-Policy'];
    $assert(str_contains($csp, "default-src 'self'"));
    $assert(str_contains($csp, "script-src 'self' 'nonce-AbCdEfGhIjKlMnOpQrStUvWxYz012345'"));
    $assert(str_contains($csp, 'wss://admin.wcp360.invalid'));
    $assert(!str_contains($csp, 'unsafe-inline') && !str_contains($csp, 'unsafe-eval'));
    $assert($headers['X-Frame-Options'] === 'DENY');
    $assert($headers['Referrer-Policy'] === 'no-referrer');
    $assert($headers['Strict-Transport-Security'] === 'max-age=31536000');
    $expectViolation(static fn() => SecurityHeadersPolicy::headers('short'));
    $expectViolation(static fn() => SecurityHeadersPolicy::headers('AbCdEfGhIjKlMnOpQrStUvWxYz012345', ['https://not-wss.invalid/path']));
});

$test('action registry rejects handlers outside canonical contract catalogue', static function () use ($contract, $expectViolation, $assert): void {
    $contracts = new ApiContractRegistry([$contract()]);
    $catalog = ActionCatalog::fromApiContracts($contracts);
    $assert($catalog->count() === 1 && $catalog->domains() === ['applications']);
    $bad = new class implements ActionHandler {
        public function actionId(): string { return 'ghost.execute'; }
        public function handle(array $input, AuthorizationContext $context): array { return []; }
    };
    $expectViolation(static fn() => new ActionRegistry($catalog, [$bad]));
});

$passed = 0;
foreach ($tests as $name => $fn) {
    try {
        $fn();
        fwrite(STDOUT, "PASS  {$name}\n");
        ++$passed;
    } catch (Throwable $e) {
        fwrite(STDERR, "FAIL  {$name}: {$e->getMessage()}\n");
        exit(1);
    }
}

fwrite(STDOUT, sprintf("%d/%d tests passed\n", $passed, count($tests)));
