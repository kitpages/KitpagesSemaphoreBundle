<?php
namespace Kitpages\SemaphoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kitpages\SemaphoreBundle\Manager\Manager;

class ReleaseCommand
    extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kitpages:semaphore:release')
            ->setHelp("Releases the blocking semaphore from the command line")
            ->setDescription('Releases the blocking semaphore from the command line')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'key of the semaphore to acquire'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument('key');
        /** @var Manager $semaphoreManager */
        $semaphoreManager = $this->getContainer()->get("kitpages_semaphore.manager");
        $semaphoreManager->release($key);
   }
}