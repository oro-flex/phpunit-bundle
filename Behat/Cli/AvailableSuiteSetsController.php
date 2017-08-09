<?php

namespace Oro\Bundle\TestFrameworkBundle\Behat\Cli;

use Behat\Testwork\Cli\Controller;
use Oro\Bundle\TestFrameworkBundle\Behat\Suite\SuiteConfiguration;
use Oro\Bundle\TestFrameworkBundle\Behat\Suite\SuiteConfigurationRegistry;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AvailableSuiteSetsController implements Controller
{
    /**
     * @var SuiteConfigurationRegistry
     */
    private $suiteConfigRegistry;

    /**
     * @param SuiteConfigurationRegistry $suiteConfigRegistry
     */
    public function __construct(SuiteConfigurationRegistry $suiteConfigRegistry)
    {
        $this->suiteConfigRegistry = $suiteConfigRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(SymfonyCommand $command)
    {
        $command
            ->addOption(
                '--available-suite-sets',
                null,
                InputOption::VALUE_NONE,
                'Output all available suite sets'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('available-suite-sets')) {
            return;
        }

        /**
         * @var string $set
         * @var SuiteConfiguration[] $chunks
         */
        foreach ($this->suiteConfigRegistry->getSets() as $set => $chunks) {
            $output->writeln($set);

            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                foreach ($chunks as $chunk) {
                    $output->writeln(sprintf('    %s', $chunk->getName()));

                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERBOSE) {
                        foreach ($chunk->getPaths() as $path) {
                            $output->writeln(sprintf('        %s', $path));
                        }
                    }
                }
            }
        }

        return 0;
    }
}
