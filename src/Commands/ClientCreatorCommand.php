<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vinelab\ClientGenerator\Storage\ClientStorage;
use Vinelab\ClientGenerator\Traits\GeneratorTrait;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\Redis\RedisKeysManager;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
class ClientCreatorCommand extends Command
{
    use GeneratorTrait;
    /**
     * Command for creating a new client.
     *
     * @var string
     */
    protected $commandName = 'clients:create';

    /**
     * Client creation command description.
     *
     * @var string
     */
    protected $commandDescription = 'Generates a new client.';

    /**
     * The name argument.
     *
     * @var string
     */
    protected $name = 'name';

    /**
     * The name argument description.
     *
     * @var string
     */
    protected $nameDescription = 'Name of the app.';

    /**
     * The redirect_uri argument.
     *
     * @var string
     */
    protected $redirectUri = 'redirect_uri';

    /**
     * The redirect_uri argument description.
     *
     * @var string
     */
    protected $redirectUriDescription = 'Redirect Uri.';

    /**
     * Default redirect_uri argument.
     *
     * @var string
     */
    protected $defaultRedirectUri = '';

    /**
     * The grantType argument.
     *
     * @var string
     */
    protected $grantType = 'grantType';

    /**
     * Default grantType argument.
     *
     * @var string
     */
    protected $defaultGrantType = 'client_credentials';

    /**
     * The grantType argument description.
     *
     * @var string
     */
    protected $grantTypeDescription = 'The grant type to use with OAuth2.';

    /**
     * The password argument.
     *
     * @var string
     */
    protected $password = 'password';

    /**
     * The password argument description.
     *
     * @var string
     */
    protected $passwordDescription = 'The password needed to delete or update a specific user.';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->name,
                InputArgument::REQUIRED,
                $this->nameDescription
            )
            ->addArgument(
                $this->password,
                InputArgument::REQUIRED,
                $this->passwordDescription
            )
            ->addArgument(
                $this->redirectUri,
                InputArgument::OPTIONAL,
                $this->redirectUriDescription,
                $this->defaultRedirectUri
            )
            ->addArgument(
                $this->grantType,
                InputArgument::OPTIONAL,
                $this->grantTypeDescription,
                $this->defaultGrantType
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appName = $input->getArgument($this->name);
        $password = $input->getArgument($this->password);
        $redirectUri = $input->getArgument($this->redirectUri);
        $grantType = $input->getArgument($this->grantType);

        $clientStorage = new ClientStorage(new RedisClient(null, 'dev', 6379), new RedisKeysManager());

        if ($client = $clientStorage->create($this->generateUuid(), $appName, $password, $this->generateUuid(), $redirectUri, $grantType)) {
            $output->writeln('<info>Your client has been generated successfully!</info>');
        } else {
            $output->writeln('<error>There was an error when creating your client...!</error>');
        }
    }
}
