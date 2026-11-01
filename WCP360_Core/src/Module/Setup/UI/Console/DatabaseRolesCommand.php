<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabasePolicy;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabaseRoles;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:database:roles', description: 'Inspect or explicitly create fresh development PostgreSQL roles; never production provisioning.')]
final class DatabaseRolesCommand extends Command
{
    public function __construct(private readonly CandidateDatabaseRoles $roles, private readonly RuntimeContext $runtime, private readonly bool $enabled = false)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'Create fresh candidate roles and grants in one transaction. Existing roles are refused.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->runtime->context !== 'setup' || ($input->getOption('apply') && !$this->enabled)) {
            $this->result($output, ['status' => 'refused']);
            return self::INVALID;
        }
        try {
            $result = $input->getOption('apply') ? $this->roles->provision($this->passwords()) : $this->roles->plan();
            $this->result($output, ['status' => $result['applied'] ? 'created' : 'planned', 'plan' => $result]);
            return self::SUCCESS;
        } catch (\Throwable $error) {
            $this->result($output, ['status' => $error->getMessage() === CandidateDatabaseRoles::UNCERTAIN ? 'uncertain' : 'refused']);
            return self::FAILURE;
        }
    }

    /** @return array<string,string> */
    private function passwords(): array
    {
        $passwords = [];
        foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
            $name = 'WCP_CANDIDATE_DATABASE_'.strtoupper($context).'_PASSWORD';
            $value = $_SERVER[$name] ?? $_ENV[$name] ?? getenv($name);
            $passwords[$context] = is_string($value) ? $value : '';
        }
        return $passwords;
    }

    /** @param array<string,mixed> $result */
    private function result(OutputInterface $output, array $result): void
    {
        $output->writeln(json_encode(['schema' => 'wcp360.candidate.database-role-command/1', 'production_qualified' => false] + $result, JSON_THROW_ON_ERROR));
    }
}
