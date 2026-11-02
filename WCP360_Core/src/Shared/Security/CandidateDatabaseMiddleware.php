<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

/** Checks candidate session grants on each new isolated DBAL connection. */
final readonly class CandidateDatabaseMiddleware implements Middleware
{
    public function __construct(private string $mode, private string $context)
    {
    }

    public function wrap(Driver $driver): Driver
    {
        if ($this->mode === 'shared') {
            return $driver;
        }
        if ($this->mode !== 'session-isolated' || !in_array($this->context, ['user', 'admin', 'worker', 'setup'], true)) {
            throw new \RuntimeException(CandidateDatabaseUrl::REJECTED);
        }

        return new class($driver, $this->context) extends AbstractDriverMiddleware {
            public function __construct(Driver $driver, private readonly string $context)
            {
                parent::__construct($driver);
            }

            public function connect(#[\SensitiveParameter] array $params): Connection
            {
                try {
                    if (($params['driver'] ?? '') !== 'pdo_pgsql') {
                        throw new \RuntimeException('PostgreSQL is required.');
                    }
                    $connection = parent::connect($params);
                    if ($this->context !== 'setup') {
                        CandidateSessionGrants::verify($connection, $this->context);
                    }

                    return $connection;
                } catch (\Throwable) {
                    // Do not attach driver exceptions: they can contain private connection values.
                    throw new \RuntimeException(CandidateDatabaseUrl::REJECTED);
                }
            }
        };
    }
}
