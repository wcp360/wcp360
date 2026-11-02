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

#[AsCommand(name: 'wcp:candidate:database:roles', description: 'Inspect candidate PostgreSQL roles, create fresh roles or explicitly enable fixed password/audit operations; never production provisioning.')]
final class DatabaseRolesCommand extends Command
{
    public function __construct(private readonly CandidateDatabaseRoles $roles, private readonly RuntimeContext $runtime, private readonly bool $enabled = false)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('audit-reads', null, InputOption::VALUE_NONE, 'Inspect or explicitly enable fixed ADMIN candidate audit metadata reads on existing verified roles.');
        $this->addOption('password-changes', null, InputOption::VALUE_NONE, 'Inspect or explicitly enable the fixed password operations on existing verified candidate roles.');
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'Apply the selected role creation or fixed capability upgrade in one transaction.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->runtime->context !== 'setup' || ($input->getOption('apply') && !$this->enabled)
            || ($input->getOption('password-changes') && $input->getOption('audit-reads'))) {
            $this->result($output, ['status' => 'refused']);
            return self::INVALID;
        }
        try {
            $audit = $input->getOption('audit-reads');
            $upgrade = $input->getOption('password-changes') || $audit;
            $apply = $input->getOption('apply');
            if ($audit) {
                $result = $apply ? $this->roles->enableAuditReads() : $this->roles->auditReadPlan();
            } elseif ($input->getOption('password-changes')) {
                $result = $apply ? $this->roles->enablePasswordChanges() : $this->roles->passwordChangePlan();
            } else {
                $result = $apply ? $this->roles->provision($this->passwords()) : $this->roles->plan();
            }
            $this->result($output, ['status' => $result['applied'] ? ($upgrade ? 'enabled' : 'created') : 'planned', 'plan' => $result]);
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
