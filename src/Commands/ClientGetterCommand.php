<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
    protected $commandDescription = 'Fetch a specific client clients.';

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
    protected $commandOptionDescription = 'If set, it will show the specific client\'s secret.';

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
    protected $commandArgumentDescription = 'Which client do you want to fetch?';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentDescription
            )
            ->addOption(
               $this->commandShowSecret,
               null,
               InputOption::VALUE_REQUIRED,
               $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->commandArgumentName);

        if ($password = $input->getOption($this->commandShowSecret)) {
            $output->writeln('<info>Your client\'s secret is: </info>');
        } else {
            $output->writeln('<info>Your client\'s information are: </info>');
        }
    }
}
