<?php

declare(strict_types=1);

use Wcp360\Core\Kernel;

require dirname(__DIR__, 2).'/vendor/autoload.php';

// Static analysis must never load an operator's .env or use a live database.
$values = [
    'APP_ENV' => 'test',
    'APP_DEBUG' => '1',
    'APP_SECRET' => 'static-analysis-only-synthetic-secret',
    'DATABASE_URL' => 'sqlite:///:memory:',
    'WCP_CANDIDATE' => '1',
    'WCP_CONTEXT' => 'user',
    'WCP_PUBLIC_HOST' => 'user.localhost',
    'WCP_ALLOW_ISOLATED_PROVISIONING' => '0',
    'WCP_ALLOW_FRANKENPHP_PLANS' => '0',
    'WCP_PLANNER' => dirname(__DIR__, 2).'/var/bin/wcp-plan'.(PHP_OS_FAMILY === 'Windows' ? '.exe' : ''),
];

foreach ($values as $name => $value) {
    $_SERVER[$name] = $_ENV[$name] = $value;
    putenv($name.'='.$value);
}

return new Kernel('test', true);
