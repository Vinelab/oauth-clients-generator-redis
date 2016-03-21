<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\Redis\RedisKeysManager;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
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
    private $showSecret = 'show-secret';

    /**
     * Show secret command option description.
     *
     * @var string
     */
    private $showSecretDescription = 'If set, it will show the specific client\'s secret.';

    /**
     * The client_id argument.
     *
     * @var string
     */
    private $clientId = 'client_id';

    /**
     * The client_id argument description.
     *
     * @var string
     */
    private $clientIdDescription = 'Which client do you want to fetch?';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->clientId,
                InputArgument::REQUIRED,
                $this->clientIdDescription
            )
            ->addOption(
               $this->showSecret,
               null,
               InputOption::VALUE_REQUIRED,
               $this->showSecretDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->clientId);

        $clientStorage = new ClientStorage(new RedisClient(null, 'dev', 6379), new RedisKeysManager());

        if ($password = $input->getOption($this->showSecret)) {
            $client = $clientStorage->read($clientId, $password);

            if ($client->getSecret()) {
                $output->writeln('<info>Your client\'s information are: </info>');
                $output->writeln('<info>App Name: </info>'.$client->getName());
                $output->writeln('<info>Client ID: </info>'.$client->getClientId());
                $output->writeln('<info>Client Secret: </info>'.$client->getSecret());
            } else {
                $output->writeln('<error>Your password is invalid!</error>');
            }
        } else {
            if ($clientStorage->read($clientId)) {
                $output->writeln('<info>Your client\'s information are: </info>');
                $output->writeln('<info>App Name: </info>'.$client->getName());
                $output->writeln('<info>Client ID: </info>'.$client->getClientId());
            }
        }
    }
}
