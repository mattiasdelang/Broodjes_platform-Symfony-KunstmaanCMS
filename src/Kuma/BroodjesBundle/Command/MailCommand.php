<?php
namespace Kuma\BroodjesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('broodjes:mail')
            // the short description shown while running "php bin/console list"
            ->setDescription('Mails the orders of the day and does the payments')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to mail the orders of the day and it will do the payments");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer = $this->getContainer()->get('kumabroodjesbundle.helper.service.mailer');
        $mailer->defaultOrder();
        $mailer->mail();
    }
}
