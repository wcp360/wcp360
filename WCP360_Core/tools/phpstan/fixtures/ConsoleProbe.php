<?php

declare(strict_types=1);

namespace Wcp360\Core\AnalysisFixtures;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wcp:analysis:probe')]
final class ConsoleProbe extends Command
{
    protected function configure(): void
    {
        $this->addOption('known', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($input->getOption('known') ? 'known' : 'none');
        $input->getOption('nonexistent-option');

        return self::SUCCESS;
    }
}
