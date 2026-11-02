<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\Setup\Application\IssuePasswordRecoveryCode;
use Wcp360\Core\Module\Setup\Application\RecoverAccountPassword;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:account:recovery-code', description: 'Inspect or explicitly issue a short-lived private recovery code; never sends email.')]
final class IssuePasswordRecoveryCodeCommand extends Command
{
    public function __construct(private readonly RecoverAccountPassword $accounts,
        private readonly IssuePasswordRecoveryCode $issuer, private readonly RuntimeContext $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Exact current account username.');
        $this->addOption('surface', null, InputOption::VALUE_REQUIRED, 'Required account surface: user or admin.');
        $this->addOption('issue', null, InputOption::VALUE_NONE, 'Confirm and display a private code once; deliver it through a verified private channel.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $issue = (bool) $input->getOption('issue');
        if ($this->runtime->context !== 'setup' || ($issue && (!$this->issuer->enabled() || !$input->isInteractive()))) {
            $output->writeln('Recovery code issuance refused.');
            return self::INVALID;
        }
        $username = $input->getArgument('username');
        $surface = $input->getOption('surface');
        if (!is_string($surface) || ($issue && !$this->issuer->enabled($surface))) return self::INVALID;
        try {
            $plan = $this->accounts->plan($username, $surface);
            if (!$issue) {
                $output->writeln(json_encode(['schema' => 'wcp360.candidate.recovery-code-plan/1', 'account' => ['id' => $plan['id'], 'username' => $username, 'surface' => $surface],
                    'issue_enabled' => $this->issuer->enabled($surface), 'lifetime_seconds' => PasswordRecoveryCode::LIFETIME,
                    'production_qualified' => false], JSON_THROW_ON_ERROR));
                return self::SUCCESS;
            }
            $io = new SymfonyStyle($input, $output);
            if (!$io->confirm('Display a private 15-minute recovery code for '.$surface.'/'.$username.'? Do not record or send it through a public channel.', false)) {
                $io->text('Recovery code issuance cancelled.');
                return self::SUCCESS;
            }
            $code = $this->issuer->issue($plan['id'], $username, $surface);
            $io->text('Open /reset-password on the '.$surface.' surface and paste this private code:');
            $output->writeln($code);
            unset($code);
            return self::SUCCESS;
        } catch (\Throwable) {
            $output->writeln('Recovery code issuance could not be confirmed. Inspect the maintenance audit before retrying.');
            return self::FAILURE;
        }
    }
}
