<?php
namespace Kuma\BroodjesBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DashboardCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget')
            ->setDescription('BroodjesBundle dashboard');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
