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
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\RuntimeContext;
#[AsCommand(name:'wcp:candidate:enroll', description:'Interactively enroll a local development account; never a production installer.')]
final class SetupCommand extends Command
{
    public function __construct(private readonly WorkbenchStore $store,private readonly UserPasswordHasherInterface $hasher,private readonly RuntimeContext $runtime){parent::__construct();}
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        $io=new SymfonyStyle($input,$output);
        if($this->runtime->context!=='setup' || !$input->isInteractive()){ $io->error('Use an interactive terminal with WCP_CONTEXT=setup.');return self::FAILURE;}
        $email=$io->ask('Email',null,static function($v){if(!is_string($v)||!filter_var($v,FILTER_VALIDATE_EMAIL)||strlen($v)>254)throw new \RuntimeException('Valid email required.');return strtolower($v);});
        $context=$io->choice('Account context',['user','admin'],'user');
        $password=$io->askHidden('Password (at least 14 characters)',static function($v){if(!is_string($v)||strlen($v)<14||strlen($v)>256)throw new \RuntimeException('Use 14–256 characters.');return $v;});
        $id=WorkbenchStore::id();
        $principal=new Principal($id,$email,'',$context);
        $hash=$this->hasher->hashPassword($principal,$password);
        unset($password);
        $this->store->db->transactional(function()use($id,$email,$context,$hash):void{
            $this->store->db->insert('wcp_candidate_users',['id'=>$id,'email'=>$email,'password_hash'=>$hash,'context'=>$context,'active'=>1]);
            if($context==='user'){
                $account=WorkbenchStore::id();$tenant=WorkbenchStore::id();
                $this->store->db->insert('wcp_candidate_accounts',['id'=>$account,'name'=>'Local account']);
                $this->store->db->insert('wcp_candidate_tenants',['id'=>$tenant,'account_id'=>$account,'name'=>'Development tenant']);
                $this->store->db->insert('wcp_candidate_memberships',['user_id'=>$id,'tenant_id'=>$tenant,'permission'=>'candidate.hosting.plan']);
            }
            $this->store->audit($id,null,'candidate.account.enrolled',$id);
        });
        $io->success('Development account created. Credentials were not written to logs.');return self::SUCCESS;
    }
}
