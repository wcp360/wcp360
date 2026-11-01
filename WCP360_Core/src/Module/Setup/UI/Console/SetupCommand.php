<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:enroll', description: 'Interactively enroll a local development account; never a production installer.')]
final class SetupCommand extends Command
{
    public function __construct(private readonly WorkbenchStore $store, private readonly UserPasswordHasherInterface $hasher,
        private readonly RuntimeContext $runtime) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($this->runtime->context !== 'setup' || !$input->isInteractive()) {
            $io->error('Use an interactive terminal with WCP_CONTEXT=setup.');
            return self::FAILURE;
        }
        $username = $io->ask('Username', null, static function ($value): string {
            if (!is_string($value)) throw new \RuntimeException('Username required.');
            return CandidateUsername::require($value);
        });
        $email = $io->ask('Contact email', null, static function ($value): string {
            if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL) || strlen($value) > 254) {
                throw new \RuntimeException('Valid contact email required.');
            }
            return strtolower($value);
        });
        $context = $io->choice('Account context', ['user', 'admin'], 'user');
        $password = $io->askHidden('Password (at least 14 characters)', static function ($value): string {
            if (!is_string($value) || strlen($value) < 14 || strlen($value) > 256) throw new \RuntimeException('Use 14–256 characters.');
            return $value;
        });
        $id = WorkbenchStore::id();
        $principal = new Principal($id, $username, $email, '', $context);
        $hash = $this->hasher->hashPassword($principal, $password);
        unset($password);
        $this->store->db->transactional(function () use ($id, $username, $email, $context, $hash): void {
            $this->store->db->insert('wcp_candidate_users', ['id' => $id, 'username' => $username, 'email' => $email,
                'password_hash' => $hash, 'context' => $context, 'active' => 1]);
            if ($context === 'user') {
                $account = WorkbenchStore::id(); $tenant = WorkbenchStore::id();
                $this->store->db->insert('wcp_candidate_accounts', ['id' => $account, 'name' => 'Local account']);
                $this->store->db->insert('wcp_candidate_tenants', ['id' => $tenant, 'account_id' => $account, 'name' => 'Development tenant']);
                $this->store->db->insert('wcp_candidate_memberships', ['user_id' => $id, 'tenant_id' => $tenant, 'permission' => 'candidate.hosting.plan']);
            }
            $this->store->audit($id, null, 'candidate.account.enrolled', $id);
        });
        $io->success('Development account created with username '.$username.'. The password was not written to logs.');

        return self::SUCCESS;
    }
}
