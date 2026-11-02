<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Wcp360\Contracts\ContractIntegrity;
use Wcp360\Contracts\PermissionRegistry;
use Wcp360\Database\DatabaseCatalog;
use Wcp360\Security\SessionSurfacePolicy;

$platformRoot = dirname(__DIR__);
$repositoryRoot = dirname($platformRoot, 2);
$contractsDir = $platformRoot . '/contracts';
$counts = ContractIntegrity::verify($repositoryRoot, $contractsDir);

$permissions = PermissionRegistry::fromJsonFile($contractsDir . '/permissions.v1.json');
if ($permissions->count() !== 82) {
    throw new RuntimeException('permission registry loader count mismatch');
}

$database = DatabaseCatalog::fromJsonFile($contractsDir . '/database-tables.v1.json');
$jobs = $database->require('wcp_ops.jobs');
foreach (['job_id','job_state','outcome_class','error_code','correlation_id','revision'] as $column) {
    $jobs->requireColumn($column);
}
$idempotency = $database->require('wcp_ops.idempotency_records');
foreach (['scope_kind','operation_namespace','idempotency_key','request_fingerprint','revision'] as $column) {
    $idempotency->requireColumn($column);
}
$database->require('wcp_iam.user_sessions');
$database->require('wcp_iam.admin_sessions');
SessionSurfacePolicy::fromJsonFile($contractsDir . '/session-surfaces.v1.json');

foreach ($counts as $name => $count) {
    fwrite(STDOUT, sprintf("PASS  %-18s %d\n", $name, $count));
}
fwrite(STDOUT, "PASS  canonical source Git blob fingerprints match\n");
fwrite(STDOUT, "PASS  DB jobs/idempotency/session physical anchors present\n");
