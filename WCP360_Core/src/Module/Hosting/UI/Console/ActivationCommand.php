<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\UI\Console;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Module\Hosting\Application\RunActivation;
#[AsCommand(name:'wcp:candidate:activate',description:'Process one isolated-container PHP-FPM activation; never a production provisioner.')]
final class ActivationCommand extends Command
{
    public function __construct(private readonly RunActivation $worker,private readonly RuntimeContext $runtime){parent::__construct();}
    protected function configure():void{$this->addOption('reconcile',null,InputOption::VALUE_NONE,'Observe uncertain outcomes without applying again. Stop other activation workers first.');}
    protected function execute(InputInterface $input,OutputInterface $output):int
    {
        if($this->runtime->context!=='worker'){ $output->writeln('Worker context required.');return self::FAILURE;}
        $output->writeln($input->getOption('reconcile')?(string)$this->worker->reconcile():($this->worker->runOne()??'No queued activation.'));
        return self::SUCCESS;
    }
}
