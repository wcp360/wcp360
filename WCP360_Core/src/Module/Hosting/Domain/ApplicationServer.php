<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Domain;

enum ApplicationServer: string
{
    case PhpFpm = 'php_fpm';
    case FrankenPhpWorker = 'frankenphp_worker';
}
