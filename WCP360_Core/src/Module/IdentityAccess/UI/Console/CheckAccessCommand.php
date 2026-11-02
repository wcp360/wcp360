<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:access:check', description: 'Read-only candidate tenant access check; no grant or production authorization.')]
final class CheckAccessCommand extends Command
{
    public function __construct(private readonly CandidateAuthorization $authorization, private readonly RuntimeContext $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('actor', InputArgument::REQUIRED, 'Candidate user UUID.');
        $this->addArgument('tenant', InputArgument::REQUIRED, 'Candidate tenant UUID.');
        $this->addOption('permission', null, InputOption::VALUE_REQUIRED, 'Only the existing candidate permission is implemented.', 'candidate.hosting.plan');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!in_array($this->runtime->context, ['worker', 'setup'], true)) {
            $this->writeResult($output, false);

            return self::INVALID;
        }
        $actor = $input->getArgument('actor');
        $tenant = $input->getArgument('tenant');
        $permission = $input->getOption('permission');
        $allowed = $this->authorization->allowsTenantPermission($actor, $tenant, $permission);
        $this->writeResult($output, $allowed);

        return $allowed ? self::SUCCESS : self::FAILURE;
    }

    private function writeResult(OutputInterface $output, bool $allowed): void
    {
        $output->writeln(json_encode(['schema' => 'wcp360.candidate.access-check/1', 'allowed' => $allowed, 'production' => false], JSON_THROW_ON_ERROR));
    }
}
