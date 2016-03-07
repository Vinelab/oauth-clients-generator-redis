<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientListingCommand extends Command
{
    /**
     * Command for listing clients.
     *
     * @var string
     */
    protected $commandName = 'clients:list';

    /**
     * Client listing command description.
     *
     * @var string
     */
    protected $commandDescription = 'List all clients.';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Your clients are: </info>');
    }
}
