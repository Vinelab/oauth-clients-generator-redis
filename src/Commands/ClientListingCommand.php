<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    /**
     * Command option for showing client's secret.
     *
     * @var string
     */
    protected $commandOptionName = 'show-secret';

    /**
     * Show secret command option description.
     *
     * @var string
     */
    protected $commandOptionDescription = 'If set, it will show the specific client\'s secret.';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
               $this->commandOptionName,
               null,
               InputOption::VALUE_REQUIRED,
               $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption($this->commandOptionName)) {
            $clientId = $input->getOption($this->commandOptionName);
            $output->writeln('<info>You want to show the secret of the client with id:  </info>'.$clientId);
            $output->writeln('<info>Your client\'s secret is: </info>');
        } else {
            $output->writeln('<info>Your clients are: </info>');
        }
    }
}
