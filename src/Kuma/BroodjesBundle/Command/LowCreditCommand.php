<?php

namespace Kuma\BroodjesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LowCreditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('lowcredit:message')
            // the short description shown while running "php bin/console list"
            ->setDescription('Messages to users in private, low credit warning')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to message users in private, about low credit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer = $this->getContainer()->get('kumabroodjesbundle.helper.service.lowcredit');
        $mailer->message();
    }
}
