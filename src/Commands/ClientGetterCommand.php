<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;

class ClientGetterCommand extends Command
{
    /**
     * Command for fetching a client.
     *
     * @var string
     */
    protected $commandName = 'clients:fetch';

    /**
     * Client listing command description.
     *
     * @var string
     */
    protected $commandDescription = 'Fetch a specific client.';

    /**
     * Command option for showing client's secret.
     *
     * @var string
     */
    protected $commandShowSecret = 'show-secret';

    /**
     * Show secret command option description.
     *
     * @var string
     */
    protected $commandShowSecretDescription = 'If set, it will show the specific client\'s secret.';

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
            ->addOption(
               $this->commandShowSecret,
               null,
               InputOption::VALUE_REQUIRED,
               $this->commandShowSecretDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->commandClientId);

        $clientStorage = $this->app->make(ClientStorage::class);

        if ($password = $input->getOption($this->commandShowSecret)) {
            $client = $clientStorage->read($clientId, $password);
            $output->writeln('<info>Your client\'s secret is: </info>'.$client['secret']);
        } else {
            $client = $clientStorage->read($clientId);
            $output->writeln('<info>Your client\'s information are: </info>');
            $output->writeln('<info>Client ID: </info>'.$client['clientId']);
            $output->writeln('<info>App Name: </info>'.$client['name']);
        }
    }
}
