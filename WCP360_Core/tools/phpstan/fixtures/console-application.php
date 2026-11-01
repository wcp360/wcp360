<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Wcp360\Core\AnalysisFixtures\ConsoleProbe;

/** @var Application $application */
$application = require dirname(__DIR__).'/console-application.php';
require __DIR__.'/ConsoleProbe.php';
$application->addCommand(new ConsoleProbe());

return $application;
