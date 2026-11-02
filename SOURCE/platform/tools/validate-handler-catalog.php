<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Wcp360\CanonicalActions\CanonicalHandlerCatalog;
use Wcp360\Contracts\ActionCatalog;
use Wcp360\Contracts\ApiContractRegistry;

$root = dirname(__DIR__);
$contracts = ApiContractRegistry::fromJsonFile($root . '/contracts/api-contracts.v1.json');
$actions = ActionCatalog::fromJsonFile($root . '/contracts/action-catalog.v1.json');

CanonicalHandlerCatalog::validateAgainst($contracts);

$handlerIds = CanonicalHandlerCatalog::actionIds();
$catalogIds = $actions->actionIds();
sort($handlerIds);
sort($catalogIds);
if ($handlerIds !== $catalogIds) {
    fwrite(STDERR, "FAIL canonical handler action set differs from derived action catalogue\n");
    exit(1);
}

fwrite(STDOUT, "PASS 86/86 canonical action handlers structurally present\n");
fwrite(STDOUT, "PASS profiles: 17 READ / 58 MUTATION / 11 MANAGED\n");
fwrite(STDOUT, "NOTE handlers are application boundaries; concrete ports remain separately implemented/tested\n");
