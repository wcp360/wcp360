<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Operations\UI\Console;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Shared\Security\RuntimeContext;
#[AsCommand(name:'wcp:candidate:work', description:'Process one development hosting-plan job, without applying system configuration.')]
final class WorkCommand extends Command
{
    public function __construct(private readonly RunHostingPlan $worker,private readonly RuntimeContext $runtime){parent::__construct();}
    protected function configure():void{$this->addOption('reconcile-interrupted',null,InputOption::VALUE_NONE,'Mark up to 100 interrupted plans UNKNOWN_OUTCOME. Stop all rendering workers first; repeat as needed. A zero count can mean locked rows remain.');}
    protected function execute(InputInterface $input,OutputInterface $output):int
    {
        if($this->runtime->context!=='worker'){ $output->writeln('Worker context required.');return self::FAILURE;}
        if($input->getOption('reconcile-interrupted')){$output->writeln((string)$this->worker->markInterrupted());return self::SUCCESS;}
        $output->writeln($this->worker->runOne()??'No queued job.');return self::SUCCESS;
    }
}
