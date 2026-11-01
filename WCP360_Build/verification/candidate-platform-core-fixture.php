<?php

declare(strict_types=1);

// Trusted CLI verification fixture. Never expose this file through a web server.
namespace Wcp360\Build\Verification;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Kernel;
use Wcp360\Core\Module\ExtensionManagement\Application\PluginRegistry;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Setup\UI\Console\SetupCommand;
use Wcp360\Core\Shared\Platform\TargetPlatform;

require dirname(__DIR__, 2).'/WCP360_Core/vendor/autoload.php';

final class PlatformFixtureFailure extends \RuntimeException {}

/**
 * @phpstan-type FixtureState array{schema: string, target_profile: string, user_id: string, admin_id: string, tenant_id: string, account_id: string, job_id?: string, job_location?: string}
 */
final class PlatformCoreFixture
{
    private const USER_EMAIL = 'platform-user@example.test';
    private const ADMIN_EMAIL = 'platform-admin@example.test';
    private const PASSWORD = 'platform-only-account-password';
    private const STATE_SCHEMA = 'wcp360.candidate.platform-core-state/1';
    private const UUID_PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';

    private ContainerInterface $container;
    private WorkbenchStore $store;
    private string $target;
    private readonly string $coreRoot;
    private readonly string $statePath;
    private readonly string $cookiePath;

    public function __construct()
    {
        $this->coreRoot = dirname(__DIR__, 2).'/WCP360_Core';
        $this->statePath = $this->coreRoot.'/var/platform-core-state.json';
        $this->cookiePath = $this->coreRoot.'/var/platform-core-cookie';
    }

    public function run(string $mode): void
    {
        $this->guard($mode);
        $this->setEnvironment('WCP_CONTEXT', $mode === 'enroll' ? 'setup' : 'user');
        $this->setEnvironment('WCP_PUBLIC_HOST', 'user.localhost');
        $kernel = new Kernel('test', true);
        try {
            $kernel->boot();
            $this->container = $kernel->getContainer()->get('test.service_container');
            $this->store = $this->service(WorkbenchStore::class);
            $this->requireDatabase($mode);
            match ($mode) {
                'enroll' => $this->enroll(),
                'submit' => $this->submit(),
                'verify' => $this->verify(),
                'snapshot' => $this->snapshot(),
                default => throw new PlatformFixtureFailure('Unknown fixture mode.'),
            };
            if ($mode !== 'snapshot') {
                fwrite(STDOUT, 'PASS: platform Core fixture '.$mode.".\n");
            }
        } finally {
            $kernel->shutdown();
        }
    }

    private function guard(string $mode): void
    {
        if (!in_array($mode, ['enroll', 'submit', 'verify', 'snapshot'], true)
            || PHP_OS_FAMILY !== 'Linux' || !is_file('/.dockerenv')
            || !function_exists('posix_geteuid') || posix_geteuid() === 0
            || $this->environment('WCP_CANDIDATE') !== '1'
            || !in_array($this->environment('APP_ENV'), ['dev', 'test'], true)) {
            throw new PlatformFixtureFailure('An enabled, non-root disposable development container is required.');
        }
        $profile = TargetPlatform::detectHost();
        if ($profile === null || $profile['id'] !== $this->environment('WCP_TARGET_PROFILE')) {
            throw new PlatformFixtureFailure('The actual operating system does not match the requested target.');
        }
        $this->target = $profile['id'];
        $raw = file_get_contents($this->coreRoot.'/config/platforms/runtime-providers.json');
        $manifest = is_string($raw) ? json_decode($raw, true, 16, JSON_THROW_ON_ERROR) : null;
        if (!is_array($manifest) || !isset($manifest['php']) || !is_array($manifest['php'])
            || ($manifest['php']['series'] ?? null) !== '8.4'
            || ($manifest['php']['version'] ?? null) !== PHP_VERSION
            || ($manifest['php']['prefix'] ?? null) !== '/opt/wcp360/runtime/php/8.4'
            || realpath(PHP_BINARY) !== '/opt/wcp360/runtime/php/8.4/bin/php') {
            throw new PlatformFixtureFailure('The reviewed source-built PHP runtime is required.');
        }
        $url = $this->environment('DATABASE_URL');
        $parts = parse_url($url);
        $allowedDatabases = $mode === 'snapshot' ? ['/wcp360_workbench', '/wcp360_restore_test'] : ['/wcp360_workbench'];
        if (!is_array($parts) || !in_array($parts['scheme'] ?? null, ['postgresql', 'postgres', 'pgsql'], true)
            || !in_array($parts['path'] ?? null, $allowedDatabases, true)) {
            throw new PlatformFixtureFailure('The dedicated PostgreSQL verification database is required.');
        }
        $this->setEnvironment('DATABASE_URL', $url);
        umask(0077);
    }

    private function requireDatabase(string $mode): void
    {
        $db = $this->store->db;
        $allowed = $mode === 'snapshot' ? ['wcp360_workbench', 'wcp360_restore_test'] : ['wcp360_workbench'];
        if (!$db->getDatabasePlatform() instanceof PostgreSQLPlatform
            || !in_array($db->fetchOne('SELECT current_database()'), $allowed, true)) {
            throw new PlatformFixtureFailure('The actual database is outside the fixture boundary.');
        }
        $version = $db->fetchOne('SHOW server_version_num');
        if ((!is_string($version) && !is_int($version)) || !ctype_digit((string) $version)
            || (int) $version < 170000 || (int) $version >= 180000) {
            throw new PlatformFixtureFailure('The actual PostgreSQL server must be version 17.');
        }
    }

    private function enroll(): void
    {
        if (file_exists($this->statePath) || is_link($this->statePath)
            || file_exists($this->cookiePath) || is_link($this->cookiePath)) {
            throw new PlatformFixtureFailure('Enrollment requires fresh private fixture files.');
        }
        foreach (['users', 'accounts', 'tenants', 'memberships', 'sites', 'jobs', 'plugins', 'audit'] as $table) {
            if ((int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_'.$table) !== 0) {
                throw new PlatformFixtureFailure('Enrollment requires an empty migrated workbench database.');
            }
        }
        $hasher = $this->service(UserPasswordHasherInterface::class);
        $identities = [];
        $hashes = [];
        foreach ([[self::USER_EMAIL, '0', 'user'], [self::ADMIN_EMAIL, '1', 'admin']] as [$email, $choice, $context]) {
            $tester = new CommandTester($this->service(SetupCommand::class));
            $tester->setInputs([$email, $choice, self::PASSWORD]);
            if ($tester->execute([], ['interactive' => true]) !== 0 || str_contains($tester->getDisplay(), self::PASSWORD)) {
                throw new PlatformFixtureFailure('Interactive account enrollment failed or exposed the fixture password.');
            }
            $row = $this->store->db->fetchAssociative('SELECT id,email,context,active,password_hash FROM wcp_candidate_users WHERE email=? AND context=?', [$email, $context]);
            if ($row === false) {
                throw new PlatformFixtureFailure('An enrolled account is missing.');
            }
            $id = $this->stringField($row, 'id');
            $hash = $this->stringField($row, 'password_hash');
            $principal = new Principal($id, $email, $hash, $context);
            if (!Uuid::isValid($id) || !str_starts_with($hash, '$argon2id$')
                || !$hasher->isPasswordValid($principal, self::PASSWORD)
                || !in_array($row['active'], [1, '1'], true)) {
                throw new PlatformFixtureFailure('Enrollment did not preserve the expected hashed identity.');
            }
            $identities[$context] = $id;
            $hashes[$context] = $hash;
        }
        if (!isset($identities['user'], $identities['admin'], $hashes['user'], $hashes['admin'])) {
            throw new PlatformFixtureFailure('Both separate account contexts must be enrolled.');
        }
        $tenant = $this->store->db->fetchAssociative('SELECT t.id,t.account_id FROM wcp_candidate_tenants t JOIN wcp_candidate_memberships m ON m.tenant_id=t.id WHERE m.user_id=?', [$identities['user']]);
        if ($tenant === false) {
            throw new PlatformFixtureFailure('The user tenant is missing.');
        }
        $tenantId = $this->stringField($tenant, 'id');
        $accountId = $this->stringField($tenant, 'account_id');
        if (!Uuid::isValid($tenantId) || !Uuid::isValid($accountId)
            || count(array_unique([$identities['user'], $identities['admin'], $tenantId, $accountId])) !== 4
            || $hashes['user'] === $hashes['admin']
            || (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_users') !== 2
            || (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships WHERE user_id=?', [$identities['admin']]) !== 0
            || (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.account.enrolled'") !== 2) {
            throw new PlatformFixtureFailure('User/admin, account and tenant enrollment boundaries failed.');
        }
        $this->writeState([
            'schema' => self::STATE_SCHEMA, 'target_profile' => $this->target,
            'user_id' => $identities['user'], 'admin_id' => $identities['admin'],
            'tenant_id' => $tenantId, 'account_id' => $accountId,
        ], true);
    }

    private function submit(): void
    {
        $state = $this->readState(false);
        if ($this->jobCount() !== 0) {
            throw new PlatformFixtureFailure('Plan submission requires the freshly enrolled workbench.');
        }
        $this->createPrivateFile($this->cookiePath, '');
        $this->login();
        $dashboard = $this->request('GET', '/');
        if ($dashboard['status'] !== 200) {
            throw new PlatformFixtureFailure('The authenticated dashboard is unavailable '.$this->responseDescription($dashboard).'.');
        }
        $fields = $this->form($dashboard['body'], '/workbench/plans', ['_token', 'idempotency_key', 'tenant_id', 'hostname', 'application_server']);
        if ($fields['tenant_id'] !== $state['tenant_id'] || $fields['application_server'] !== 'inherit'
            || $fields['_token'] === '' || preg_match('/\A[A-Za-z0-9_-]{16,128}\z/D', $fields['idempotency_key']) !== 1) {
            throw new PlatformFixtureFailure('The actual plan form has unexpected tenant/runtime fields.');
        }
        $fields['hostname'] = 'platform.test';
        $forged = $fields;
        $forged['_token'] = 'invalid-platform-csrf';
        if ($this->request('POST', '/workbench/plans', $forged)['status'] !== 403
            || $this->jobCount() !== 0) {
            throw new PlatformFixtureFailure('A forged CSRF token created or admitted a job.');
        }
        $submitted = $this->request('POST', '/workbench/plans', $fields);
        if ($submitted['status'] !== 303 || !is_string($submitted['location'])
            || preg_match('~\A/workbench/jobs/('.self::UUID_PATTERN.')\z~D', $submitted['location'], $match) !== 1) {
            throw new PlatformFixtureFailure('The real plan form did not redirect to its queued job.');
        }
        $replayed = $this->request('POST', '/workbench/plans', $fields);
        if ($replayed['status'] !== 303 || $replayed['location'] !== $submitted['location']
            || $this->jobCount() !== 1) {
            throw new PlatformFixtureFailure('The identical HTTP retry did not preserve one logical job.');
        }
        $page = $this->request('GET', $submitted['location']);
        $job = $this->store->job($state['user_id'], $match[1]);
        if ($page['status'] !== 200 || !str_contains($page['body'], '>QUEUED</span>')
            || $job['state'] !== 'QUEUED' || $job['tenant_id'] !== $state['tenant_id']) {
            throw new PlatformFixtureFailure('The queued job is unavailable to its authorized user.');
        }
        $state['job_id'] = $match[1];
        $state['job_location'] = $submitted['location'];
        $this->writeState($state, false);
    }

    private function verify(): void
    {
        $state = $this->readState(true);
        $jobId = $state['job_id'] ?? throw new PlatformFixtureFailure('The queued job identity is missing.');
        $location = $state['job_location'] ?? throw new PlatformFixtureFailure('The queued job location is missing.');
        $page = $this->request('GET', $location);
        $job = $this->store->job($state['user_id'], $jobId);
        if ($page['status'] !== 200 || !str_contains($page['body'], '>SUCCEEDED</span>')
            || $job['state'] !== 'SUCCEEDED' || $job['tenant_id'] !== $state['tenant_id']
            || $job['application_server'] !== 'php_fpm') {
            throw new PlatformFixtureFailure('The actual worker did not publish the authorized PHP-FPM result.');
        }
        $siteId = $this->stringField($job, 'site_id');
        $site = $this->store->db->fetchAssociative('SELECT state,revision,hostname FROM wcp_candidate_sites WHERE id=? AND tenant_id=?', [$siteId, $state['tenant_id']]);
        $result = $this->stringField($job, 'result');
        if ($site === false || $site['state'] !== 'PLAN_READY' || (int) $site['revision'] !== 2
            || $site['hostname'] !== 'platform.test' || strlen($result) > 65536) {
            throw new PlatformFixtureFailure('The prepared site state/result boundary failed.');
        }
        $plan = json_decode($result, true, 16, JSON_THROW_ON_ERROR);
        if (!is_array($plan) || array_keys($plan) !== ['schema', 'nginx', 'php_fpm']
            || $plan['schema'] !== 'wcp360.candidate.plan/1' || !is_string($plan['nginx']) || !is_string($plan['php_fpm'])
            || !str_contains($plan['nginx'], 'server_name platform.test;')
            || !str_contains($plan['nginx'], 'root /srv/wcp360-candidate/vhosts/'.$siteId.'/public;')
            || !str_contains($plan['php_fpm'], 'clear_env = yes')) {
            throw new PlatformFixtureFailure('The bounded Go result is not the expected inert site configuration.');
        }
        $plugins = $this->service(PluginRegistry::class);
        if ($plugins->installed() !== []) {
            throw new PlatformFixtureFailure('The reference plugin must initially be absent.');
        }
        $plugins->transition($state['admin_id'], 'wcp360-reference-report', 'install');
        if (($plugins->installed()[0]['state'] ?? null) !== 'DISABLED') {
            throw new PlatformFixtureFailure('Plugin installation did not leave execution disabled.');
        }
        $plugins->transition($state['admin_id'], 'wcp360-reference-report', 'enable');
        $summary = $plugins->summary($state['user_id'], $state['tenant_id']);
        $expected = ['schema' => 'wcp360.candidate.summary/1', 'counts' => [['state' => 'SUCCEEDED', 'total' => 1]]];
        $httpSummary = $this->request('GET', '/workbench/v1/summary/'.$state['tenant_id']);
        if ($summary !== $expected || $httpSummary['status'] !== 200
            || json_decode($httpSummary['body'], true, 8, JSON_THROW_ON_ERROR) !== $expected) {
            throw new PlatformFixtureFailure('The enabled plugin summary escaped its authorized tenant or expected result.');
        }
        $cleanEnvironment = [];
        foreach (array_merge(getenv() ?: [], $_ENV, $_SERVER) as $name => $value) {
            if (is_string($name)) {
                $cleanEnvironment[$name] = false;
            }
        }
        $process = new Process([PHP_BINARY, dirname($this->coreRoot).'/WCP360_Plugins/wcp360-reference-report/bin/report.php'], null, $cleanEnvironment, json_encode($summary, JSON_THROW_ON_ERROR), 5.0);
        $process->run();
        if (!$process->isSuccessful() || str_replace("\r\n", "\n", $process->getOutput()) !== "SUCCEEDED: 1\n" || $process->getErrorOutput() !== '') {
            throw new PlatformFixtureFailure('The detached plugin failed to consume only the provided summary.');
        }
        $plugins->transition($state['admin_id'], 'wcp360-reference-report', 'disable');
        if (($plugins->installed()[0]['state'] ?? null) !== 'DISABLED'
            || $this->request('GET', '/workbench/v1/summary/'.$state['tenant_id'])['status'] !== 404) {
            throw new PlatformFixtureFailure('Plugin disablement did not revoke summary access.');
        }
        $plugins->transition($state['admin_id'], 'wcp360-reference-report', 'uninstall');
        if ($plugins->installed() !== [] || $this->store->job($state['user_id'], $jobId)['state'] !== 'SUCCEEDED'
            || $this->request('GET', $location)['status'] !== 200) {
            throw new PlatformFixtureFailure('Plugin removal interrupted the Core job.');
        }
        $dashboard = $this->request('GET', '/');
        $logout = $this->form($dashboard['body'], '/logout', ['_csrf_token']);
        $loggedOut = $this->request('POST', '/logout', $logout);
        if ($loggedOut['status'] !== 302 || $loggedOut['location'] !== '/login') {
            throw new PlatformFixtureFailure('The Core logout flow failed after plugin removal '.$this->responseDescription($loggedOut).'.');
        }
        $this->login();
        if ($this->request('GET', $location)['status'] !== 200) {
            throw new PlatformFixtureFailure('The Core login/job flow failed after plugin removal.');
        }
    }

    private function snapshot(): void
    {
        $queries = [
            'users' => 'SELECT id,context,active FROM wcp_candidate_users ORDER BY id',
            'accounts' => 'SELECT id,name FROM wcp_candidate_accounts ORDER BY id',
            'tenants' => 'SELECT id,account_id,name FROM wcp_candidate_tenants ORDER BY id',
            'memberships' => 'SELECT user_id,tenant_id,permission FROM wcp_candidate_memberships ORDER BY user_id,tenant_id,permission',
            'sites' => 'SELECT id,tenant_id,hostname,state,revision,application_server,worker_acknowledged FROM wcp_candidate_sites ORDER BY id',
            'jobs' => 'SELECT id,tenant_id,site_id,actor_id,state,result,created_at,started_at,completed_at FROM wcp_candidate_jobs ORDER BY id',
            'plugins' => 'SELECT id,version,state,manifest_hash FROM wcp_candidate_plugins ORDER BY id',
            'audit' => 'SELECT id,actor_id,tenant_id,kind,resource_id,created_at FROM wcp_candidate_audit ORDER BY id',
        ];
        $db = $this->store->db;
        $db->beginTransaction();
        try {
            $db->executeStatement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $data = [];
            $counts = [];
            foreach ($queries as $name => $query) {
                $rows = $db->fetchAllAssociative($query);
                if ($name === 'jobs') {
                    foreach ($rows as &$row) {
                        $result = $row['result'];
                        if ($result !== null && !is_string($result)) {
                            throw new PlatformFixtureFailure('A stored job result has an unexpected type.');
                        }
                        unset($row['result']);
                        $row['result_sha256'] = $result === null ? null : hash('sha256', $result);
                    }
                    unset($row);
                }
                $data[$name] = $rows;
                $counts[$name] = count($rows);
            }
            $db->commit();
        } catch (\Throwable $error) {
            $db->rollBack();
            throw $error;
        }
        fwrite(STDOUT, json_encode(['schema' => 'wcp360.candidate.platform-core-snapshot/1', 'counts' => $counts, 'data' => $data], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)."\n");
    }

    private function login(): void
    {
        $page = $this->request('GET', '/login');
        if ($page['status'] !== 200) {
            throw new PlatformFixtureFailure('The actual login form is unavailable '.$this->responseDescription($page).'.');
        }
        $fields = $this->form($page['body'], '/login', ['_username', '_password', '_csrf_token']);
        $fields['_username'] = self::USER_EMAIL;
        $fields['_password'] = self::PASSWORD;
        $response = $this->request('POST', '/login', $fields);
        if ($response['status'] !== 302 || $response['location'] !== '/') {
            throw new PlatformFixtureFailure('Real HTTP authentication failed '.$this->responseDescription($response).'.');
        }
    }

    /**
     * @param array<string, string> $fields
     * @return array{status: int, body: string, location: ?string}
     */
    private function request(string $method, string $path, array $fields = []): array
    {
        if (!in_array($method, ['GET', 'POST'], true)
            || preg_match('~\A(?:/|/login|/logout|/workbench/plans|/workbench/jobs/'.self::UUID_PATTERN.'|/workbench/v1/summary/'.self::UUID_PATTERN.')\z~D', $path) !== 1) {
            throw new PlatformFixtureFailure('The HTTP fixture is restricted to fixed loopback application paths.');
        }
        $this->requirePrivateFile($this->cookiePath);
        $handle = curl_init('http://127.0.0.1:8080'.$path);
        if ($handle === false) {
            throw new PlatformFixtureFailure('The HTTP verification client is unavailable.');
        }
        $body = '';
        $headers = '';
        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => ['Host: user.localhost:8080', 'Origin: http://user.localhost:8080', 'Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_PROXY => '',
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_COOKIEFILE => $this->cookiePath,
            CURLOPT_COOKIEJAR => $this->cookiePath,
            CURLOPT_HEADERFUNCTION => static function (\CurlHandle $curl, string $chunk) use (&$headers): int {
                if (strlen($headers) + strlen($chunk) > 65536) return 0;
                $headers .= $chunk;
                return strlen($chunk);
            },
            CURLOPT_WRITEFUNCTION => static function (\CurlHandle $curl, string $chunk) use (&$body): int {
                if (strlen($body) + strlen($chunk) > 524288) return 0;
                $body .= $chunk;
                return strlen($chunk);
            },
        ]);
        if ($method === 'POST') {
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($fields, '', '&', PHP_QUERY_RFC3986));
        }
        $ok = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_setopt($handle, CURLOPT_COOKIELIST, 'FLUSH');
        unset($handle);
        $this->requirePrivateFile($this->cookiePath);
        if ($ok === false) {
            throw new PlatformFixtureFailure('The bounded loopback HTTP request failed.');
        }
        $locations = [];
        $count = preg_match_all('/^Location:[ \t]*([^\r\n]+)\r?$/mi', $headers, $locations);
        if ($count === false || $count > 1) {
            throw new PlatformFixtureFailure('The HTTP response has ambiguous redirect headers.');
        }
        $location = $count === 1 ? $this->redirectPath(trim($locations[1][0]), $status) : null;
        return ['status' => $status, 'body' => $body, 'location' => $location];
    }

    private function redirectPath(string $location, int $status): string
    {
        // Symfony Security generates absolute success/logout URLs; controller
        // redirects use relative paths. Neither form is followed automatically.
        $parts = parse_url($location);
        if (!is_array($parts) || array_diff(array_keys($parts), ['scheme', 'host', 'port', 'path']) !== []) {
            throw new PlatformFixtureFailure('The HTTP redirect is outside the fixed fixture origin (status '.$status.').');
        }
        if (isset($parts['scheme']) || isset($parts['host']) || isset($parts['port'])) {
            if (($parts['scheme'] ?? null) !== 'http' || ($parts['host'] ?? null) !== 'user.localhost' || ($parts['port'] ?? null) !== 8080) {
                throw new PlatformFixtureFailure('The HTTP redirect is outside the fixed fixture origin (status '.$status.').');
            }
        }
        $path = $parts['path'] ?? '/';
        if (preg_match('~\A(?:/|/login|/logout|/workbench/plans|/workbench/jobs/'.self::UUID_PATTERN.'|/workbench/v1/summary/'.self::UUID_PATTERN.')\z~D', $path) !== 1) {
            throw new PlatformFixtureFailure('The HTTP redirect has an unexpected fixture path (status '.$status.').');
        }
        return $path;
    }

    /** @param array{status: int, body: string, location: ?string} $response */
    private function responseDescription(array $response): string
    {
        // Only a numeric HTTP status and an already allowlisted path are emitted.
        return '(status '.$response['status'].', redirect '.($response['location'] ?? 'none').')';
    }

    /**
     * @param list<string> $required
     * @return array<string, string>
     */
    private function form(string $html, string $action, array $required): array
    {
        $document = new \DOMDocument();
        if (!$document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new PlatformFixtureFailure('The actual HTML form could not be parsed.');
        }
        $xpath = new \DOMXPath($document);
        $forms = $xpath->query('//form[@method="post" and @action="'.$action.'"]');
        if ($forms === false || $forms->length !== 1) {
            throw new PlatformFixtureFailure('The expected unique application form is missing.');
        }
        $form = $forms->item(0);
        if (!$form instanceof \DOMElement) {
            throw new PlatformFixtureFailure('The application form element is invalid.');
        }
        $elements = $xpath->query('.//input[@name] | .//select[@name]', $form);
        if ($elements === false) {
            throw new PlatformFixtureFailure('The application form controls are invalid.');
        }
        $fields = [];
        foreach ($elements as $element) {
            if (!$element instanceof \DOMElement || $element->hasAttribute('disabled')) continue;
            $name = $element->getAttribute('name');
            if (in_array(strtolower($element->getAttribute('type')), ['checkbox', 'radio'], true) && !$element->hasAttribute('checked')) continue;
            if ($name === '' || isset($fields[$name])) {
                throw new PlatformFixtureFailure('The application form has duplicate or empty control names.');
            }
            if ($element->tagName === 'select') {
                $selected = $xpath->query('./option[@selected and not(@disabled)]', $element);
                $options = $xpath->query('./option[not(@disabled)]', $element);
                $option = $selected !== false && $selected->length > 0 ? $selected->item(0) : ($options !== false ? $options->item(0) : null);
                if (!$option instanceof \DOMElement) {
                    throw new PlatformFixtureFailure('The application selector has no enabled value.');
                }
                $fields[$name] = $option->getAttribute('value');
            } else {
                $fields[$name] = $element->getAttribute('value');
            }
        }
        if (array_diff($required, array_keys($fields)) !== []) {
            throw new PlatformFixtureFailure('Required controls are missing from the actual form.');
        }
        return $fields;
    }

    /** @return FixtureState */
    private function readState(bool $requiresJob): array
    {
        $this->requirePrivateFile($this->statePath);
        $raw = file_get_contents($this->statePath);
        $state = is_string($raw) ? json_decode($raw, true, 8, JSON_THROW_ON_ERROR) : null;
        if (!is_array($state) || ($state['schema'] ?? null) !== self::STATE_SCHEMA || ($state['target_profile'] ?? null) !== $this->target) {
            throw new PlatformFixtureFailure('Private fixture state is invalid or belongs to another target.');
        }
        $result = ['schema' => self::STATE_SCHEMA, 'target_profile' => $this->target];
        foreach (['user_id', 'admin_id', 'tenant_id', 'account_id'] as $name) {
            $id = $state[$name] ?? null;
            if (!is_string($id) || !Uuid::isValid($id)) {
                throw new PlatformFixtureFailure('A private fixture identity is invalid.');
            }
            $result[$name] = $id;
        }
        if ($requiresJob) {
            $id = $state['job_id'] ?? null;
            $location = $state['job_location'] ?? null;
            if (!is_string($id) || !Uuid::isValid($id) || $location !== '/workbench/jobs/'.$id) {
                throw new PlatformFixtureFailure('The private job reference is invalid.');
            }
            $result['job_id'] = $id;
            $result['job_location'] = $location;
        } elseif (isset($state['job_id']) || isset($state['job_location'])) {
            throw new PlatformFixtureFailure('The fixture submission has already run.');
        }
        return $result;
    }

    /** @param FixtureState $state */
    private function writeState(array $state, bool $create): void
    {
        $raw = json_encode($state, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)."\n";
        if ($create) {
            $this->createPrivateFile($this->statePath, $raw);
        } else {
            $this->requirePrivateFile($this->statePath);
            if (file_put_contents($this->statePath, $raw, LOCK_EX) !== strlen($raw)) {
                throw new PlatformFixtureFailure('Private fixture state could not be saved.');
            }
        }
    }

    private function createPrivateFile(string $path, string $content): void
    {
        $handle = @fopen($path, 'x');
        if ($handle === false) {
            throw new PlatformFixtureFailure('A fresh private fixture file could not be created.');
        }
        try {
            if (fwrite($handle, $content) !== strlen($content) || !chmod($path, 0600)) {
                throw new PlatformFixtureFailure('A private fixture file could not be initialized.');
            }
        } finally {
            fclose($handle);
        }
        $this->requirePrivateFile($path);
    }

    private function requirePrivateFile(string $path): void
    {
        clearstatcache(true, $path);
        $stat = @lstat($path);
        if ($stat === false || is_link($path) || !is_file($path) || ($stat['mode'] & 0777) !== 0600
            || $stat['uid'] !== posix_geteuid() || $stat['size'] > 16384) {
            throw new PlatformFixtureFailure('A fixture state/cookie file is not private and bounded.');
        }
    }

    /** @param array<string, mixed> $row */
    private function stringField(array $row, string $name): string
    {
        $value = $row[$name] ?? null;
        if (!is_string($value) || $value === '') {
            throw new PlatformFixtureFailure('A required persisted string is missing.');
        }
        return $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private function service(string $class): object
    {
        $value = $this->container->get($class);
        if (!$value instanceof $class) {
            throw new PlatformFixtureFailure('A required existing Core service is unavailable.');
        }
        return $value;
    }

    /**
     * The HTTP server and worker can change the database between observations.
     * @phpstan-impure
     */
    private function jobCount(): int
    {
        return (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs');
    }

    private function environment(string $name): string
    {
        $value = $_SERVER[$name] ?? $_ENV[$name] ?? getenv($name);
        return is_string($value) ? $value : '';
    }

    private function setEnvironment(string $name, string $value): void
    {
        $_SERVER[$name] = $_ENV[$name] = $value;
        putenv($name.'='.$value);
    }
}

try {
    (new PlatformCoreFixture())->run($argv[1] ?? '');
} catch (PlatformFixtureFailure $error) {
    fwrite(STDERR, 'FAIL: '.$error->getMessage()."\n");
    exit(1);
} catch (\Throwable) {
    // Never echo connection exceptions, submitted credentials, cookies or secret environment values.
    fwrite(STDERR, "FAIL: the platform Core fixture failed inside its authorized verification boundary.\n");
    exit(1);
}
