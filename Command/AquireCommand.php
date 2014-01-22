<?php
namespace Kitpages\SemaphoreBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Router;
use Kitpages\SemaphoreBundle\Manager\Manager;

class AquireCommand
    extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kitpages:semaphore:aquire')
            ->setHelp("Aquires a semaphore from the command line")
            ->setDescription('Aquires a semaphore from the command line')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'key of the semaphore to acquire'
            )
            ->addOption(
                'data',
                null,
                InputOption::VALUE_OPTIONAL,
                'data to send to the command and returned in output'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument('key');
        $data = $input->getOption('data');

        $pid = getmypid();
        $this->getContainer()->get("logger")->debug("[$pid] acquire command running, key=$key");

        /** @var Manager $semaphoreManager */
        $semaphoreManager = $this->getContainer()->get("kitpages_semaphore.manager");
        $semaphoreManager->aquire($key);

        $this->getContainer()->get("logger")->debug("[$pid] acquire command returns $data, key=$key");
        $output->write($data, false);
   }
}