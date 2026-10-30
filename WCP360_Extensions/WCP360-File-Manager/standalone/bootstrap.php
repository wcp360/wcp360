<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
require __DIR__.'/Kernel.php';
require __DIR__.'/LoginController.php';
require __DIR__.'/ShareController.php';
// Configuration is PHP returning strings, outside public/. Never source uploaded files.
$config=__DIR__.'/var/config.php';
if (!is_file($config)) { throw new \RuntimeException('Run php standalone/bin/setup.php first.'); }
foreach (require $config as $name=>$value) { $_ENV[$name]=$_SERVER[$name]=$value; }
umask(0077);
