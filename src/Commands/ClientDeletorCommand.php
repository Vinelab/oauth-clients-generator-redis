<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientDeletorCommand extends Command
{
    /**
     * Command for deleting specific client.
     *
     * @var string
     */
    protected $commandName = 'clients:delete';

    /**
     * Client deletion command description.
     *
     * @var string
     */
    protected $commandDescription = 'Deletes a specific client.';

    /**
     * The client_id argument.
     *
     * @var string
     */
    protected $commandArgumentName = 'client_id';

    /**
     * The client_id argument description.
     *
     * @var string
     */
    protected $commandArgumentDescription = 'Which client do you want to delete?';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->commandArgumentName);

        $output->writeln('<info>Your client has been deleted successfully!</info>');
    }
}
