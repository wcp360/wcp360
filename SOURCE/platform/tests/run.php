<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Wcp360\Application\ActionHandler;
use Wcp360\Application\ActionRegistry;
use Wcp360\Application\PlatformKernel;
use Wcp360\Contracts\ApiContractDefinition;
use Wcp360\Contracts\ApiContractRegistry;
use Wcp360\Contracts\PermissionDefinition;
use Wcp360\Contracts\PermissionRegistry;
use Wcp360\Contracts\RequestSchema;
use Wcp360\Ops\AuditRecord;
use Wcp360\Ops\JobRecord;
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
    $kernel = new PlatformKernel(new ApiContractRegistry([$contract()]), $service, new ActionRegistry());
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
    $kernel = new PlatformKernel(new ApiContractRegistry([$contract()]), $service, new ActionRegistry([$handler]));
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
