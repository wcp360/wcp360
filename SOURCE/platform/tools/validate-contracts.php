<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Wcp360\Contracts\ContractIntegrity;

$platformRoot = dirname(__DIR__);
$repositoryRoot = dirname($platformRoot, 2);
$counts = ContractIntegrity::verify($repositoryRoot, $platformRoot . '/contracts');

foreach ($counts as $name => $count) {
    fwrite(STDOUT, sprintf("PASS  %-18s %d\n", $name, $count));
}
fwrite(STDOUT, "PASS  canonical source Git blob fingerprints match\n");
