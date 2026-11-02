<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wcp360\Core\Module\Hosting\Application\ListApplicationCandidates;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueQuery;
use Wcp360\Core\Shared\Security\RuntimeContext;

#[AsCommand(name: 'wcp:candidate:apps:catalogue', description: 'Inspect bounded candidate application metadata; never installs or qualifies an application.')]
final class ApplicationCatalogueCommand extends Command
{
    public function __construct(private readonly ListApplicationCandidates $catalogue, private readonly RuntimeContext $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('category', null, InputOption::VALUE_REQUIRED, 'Exact candidate category.');
        $this->addOption('app-profile', null, InputOption::VALUE_REQUIRED, 'Exact proposed profile; metadata is not admission.');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Page size: 25, 50 or 100.', '25');
        $this->addOption('offset', null, InputOption::VALUE_REQUIRED, 'Offset from 0 to 2048.', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!in_array($this->runtime->context, ['worker', 'setup'], true)) {
            $this->failure($output, 'refused');
            return self::INVALID;
        }
        try {
            $query = ApplicationCatalogueQuery::fromArray([
                'category' => $input->getOption('category'), 'profile' => $input->getOption('app-profile'),
                'limit' => $input->getOption('limit'), 'offset' => $input->getOption('offset')]);
        } catch (\InvalidArgumentException) {
            $this->failure($output, 'invalid');
            return self::INVALID;
        }
        try {
            $report = $this->catalogue->list($query);
            $output->writeln(json_encode($report, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), OutputInterface::OUTPUT_RAW);
            return $report['status'] === 'available' ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable) {
            $this->failure($output, 'failed');
            return self::FAILURE;
        }
    }

    private function failure(OutputInterface $output, string $status): void
    {
        $output->writeln(json_encode(['schema' => 'wcp360.candidate.application-catalogue/1', 'status' => $status,
            'scope' => 'editorial_metadata', 'installable' => false, 'deployment_available' => false], JSON_THROW_ON_ERROR), OutputInterface::OUTPUT_RAW);
    }
}
