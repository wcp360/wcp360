<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Wcp360\Core\Kernel;

/** @var Kernel $kernel */
$kernel = require __DIR__.'/bootstrap.php';

// PHPStan inspects command definitions. It never runs their execute() methods.
return new Application($kernel);
