<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\Redis\RedisKeysManager;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
class ClientRemovalCommand extends Command
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
    private $clientId = 'client_id';

    /**
     * The client_id argument description.
     *
     * @var string
     */
    private $clientIdDescription = 'Which client do you want to delete?';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->clientId,
                InputArgument::REQUIRED,
                $this->clientIdDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->clientId);

        $clientStorage = new ClientStorage(new RedisClient(null, 'dev', 6379), new RedisKeysManager());

        if ($clientStorage->delete($clientId)) {
            $output->writeln('<info>Your client has been deleted successfully!</info>');
        } else {
            $output->writeln('<error>Error when deleting the client...!</error>');
        }
    }
}
