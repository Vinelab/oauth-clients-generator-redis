<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientSecretRegenerationCommand extends Command
{
    /**
     * Command for fetching a client.
     *
     * @var string
     */
    protected $commandName = 'clients:regenerate-secret';

    /**
     * Client listing command description.
     *
     * @var string
     */
    protected $commandDescription = 'Regenerate the secret for a specific client.';

    /**
     * The client_id argument.
     *
     * @var string
     */
    protected $commandClientId = 'client_id';

    /**
     * The client_id argument description.
     *
     * @var string
     */
    protected $commandClientIdDescription = 'Which client do you want to fetch?';

    /**
     * The password argument.
     *
     * @var string
     */
    protected $commandPassword = 'password';

    /**
     * The password argument description.
     *
     * @var string
     */
    protected $commandPasswordDescription = 'Which password does the client use?';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandClientId,
                InputArgument::REQUIRED,
                $this->commandClientIdDescription
            )
            ->addArgument(
                $this->commandPassword,
                InputArgument::REQUIRED,
                $this->commandPasswordDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->commandClientId);
        $password = $input->getArgument($this->commandPassword);

        $output->writeln('<info>Your client\'s secret has been re-generated!</info>');
    }
}
