<?php

declare(strict_types=1);

use Wcp360\Core\Kernel;

/** @var Kernel $kernel */
$kernel = require __DIR__.'/bootstrap.php';

try {
    // Debug mode rebuilds the XML when source/configuration resources change.
    $kernel->boot();
    $expected = $kernel->getCacheDir().'/Wcp360_Core_KernelTestDebugContainer.xml';
    $actual = $kernel->getContainer()->getParameter('debug.container.dump');
    if (!is_file($expected) || realpath($actual) !== realpath($expected)) {
        throw new RuntimeException('The current Symfony analysis container XML was not generated at the configured path.');
    }
    fwrite(STDOUT, "Symfony analysis container prepared (test/user, synthetic configuration).\n");
} finally {
    $kernel->shutdown();
}
