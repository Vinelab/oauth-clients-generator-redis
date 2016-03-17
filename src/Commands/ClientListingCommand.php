<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\Redis\RedisKeysManager;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
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
        $clientStorage = new ClientStorage(new RedisClient(null, 'dev', 6379), new RedisKeysManager());

        if ($clients = $clientStorage->all()) {
            $output->writeln('<info>Your clients are: </info>');
            foreach ($clients as $client) {
                $output->writeln('<info>Client ID: </info>'.$client->getClientId());
                $output->writeln('<info>App Name: </info>'.$client->getName());
                $output->writeln('-------------------------------------------------');
            }
        } else {
            $output->writeln('<error>No available clients!</error>');
        }
    }
}
