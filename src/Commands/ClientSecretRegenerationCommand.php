<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\Redis\RedisKeysManager;
use Vinelab\ClientGenerator\Traits\GeneratorTrait;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
class ClientSecretRegenerationCommand extends Command
{
    use GeneratorTrait;

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
    private $clientId = 'client_id';

    /**
     * The client_id argument description.
     *
     * @var string
     */
    private $clientIdDescription = 'Which client do you want to fetch?';

    /**
     * The password argument.
     *
     * @var string
     */
    private $password = 'password';

    /**
     * The password argument description.
     *
     * @var string
     */
    private $passwordDescription = 'Which password does the client use?';

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
            ->addArgument(
                $this->password,
                InputArgument::REQUIRED,
                $this->passwordDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument($this->clientId);
        $password = $input->getArgument($this->password);

        $clientStorage = new ClientStorage(new RedisClient(null, 'dev', 6379), new RedisKeysManager());

        $newSecret = $this->generateUuid();

        if ($clientStorage->updateSecret($clientId, $newSecret, $password)) {
            $output->writeln('<info>Your client\'s secret has been updated!</info>');
            $output->writeln('<info>Client Secret: </info>'.$newSecret);
            $output->writeln('-------------------------------------------------');
        } else {
            $output->writeln('<error>Client ID or client\'s password do not match!</error>');
        }
    }
}
