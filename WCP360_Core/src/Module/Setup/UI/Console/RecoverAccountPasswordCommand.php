<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wcp360\Core\Module\Setup\Application\RecoverAccountPassword;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:account:recover', description: 'Inspect or explicitly reset a local candidate account password from maintenance; preserves MFA.')]
final class RecoverAccountPasswordCommand extends Command
{
    public function __construct(private readonly RecoverAccountPassword $recovery, private readonly RuntimeContext $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Exact current account username.');
        $this->addOption('surface', null, InputOption::VALUE_REQUIRED, 'Required account surface: user or admin.');
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'Confirm interactively, replace the password and revoke all account sessions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apply = (bool) $input->getOption('apply');
        if ($this->runtime->context !== 'setup' || ($apply && (!$this->recovery->enabled() || !$input->isInteractive()))) {
            $this->result($output, 'refused');
            return self::INVALID;
        }
        $username = $input->getArgument('username');
        $surface = $input->getOption('surface');
        if (!is_string($surface)) {
            $this->result($output, 'refused');
            return self::INVALID;
        }
        try {
            $plan = $this->recovery->plan($username, $surface);
            if (!$apply) {
                $this->result($output, 'planned', $plan);
                return self::SUCCESS;
            }
            $io = new SymfonyStyle($input, $output);
            if (!$io->confirm('Reset the password for '.$surface.'/'.$username.' and revoke every account session? MFA will be preserved.', false)) {
                $this->result($output, 'cancelled');
                return self::SUCCESS;
            }
            $password = $this->password($io, 'New password (at least 14 characters)');
            $confirmation = $this->password($io, 'Repeat the new password');
            if (!hash_equals($password, $confirmation)) throw new \RuntimeException(RecoverAccountPassword::REFUSED);
            unset($confirmation);
            $result = $this->recovery->recover($plan['id'], $username, $surface, $password);
            unset($password);
            $this->result($output, 'recovered', $result);
            return self::SUCCESS;
        } catch (\Throwable $error) {
            $uncertain = $error->getMessage() === RecoverAccountPassword::UNCERTAIN;
            $this->result($output, $uncertain ? 'uncertain' : 'refused');
            return self::FAILURE;
        }
    }

    private function password(SymfonyStyle $io, string $prompt): string
    {
        $question = new Question($prompt);
        $question->setHidden(true)->setHiddenFallback(false)->setTrimmable(false)->setMaxAttempts(1);
        $answer = $io->askQuestion($question);
        if (!is_string($answer)) throw new \RuntimeException(RecoverAccountPassword::REFUSED);
        // Strip the Windows console helper's record ending, never passphrase spaces.
        return preg_replace('/\r?\n\z/D', '', $answer) ?? throw new \RuntimeException(RecoverAccountPassword::REFUSED);
    }

    /** @param array<string,mixed>|null $account */
    private function result(OutputInterface $output, string $status, ?array $account = null): void
    {
        $result = ['schema' => 'wcp360.candidate.account-recovery/1', 'status' => $status,
            'production_qualified' => false, 'mfa_preserved' => true];
        if ($status === 'refused') $result['message'] = RecoverAccountPassword::REFUSED;
        if ($status === 'uncertain') $result['message'] = RecoverAccountPassword::UNCERTAIN;
        if ($account !== null) $result['account'] = $account;
        $output->writeln(json_encode($result, JSON_THROW_ON_ERROR));
    }
}
