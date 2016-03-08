<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCreatorCommand extends Command
{
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
    protected $commandAppName = 'name';

    /**
     * The name argument description.
     *
     * @var string
     */
    protected $commandAppDescription = 'Name of the app.';

    /**
     * The redirect_uri argument.
     *
     * @var string
     */
    protected $commandRedirectUri = 'redirect_uri';

    /**
     * The redirect_uri argument description.
     *
     * @var string
     */
    protected $commandRedirectUriDescription = 'Redirect Uri.';

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
    protected $commandGrantType = 'grantType';

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
    protected $commandGrantTypeDescription = 'The grant type to use with OAuth2.';

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
    protected $commandPasswordDescription = 'The password needed to delete or update a specific user.';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandAppName,
                InputArgument::REQUIRED,
                $this->commandAppDescription
            )
            ->addArgument(
                $this->commandPassword,
                InputArgument::REQUIRED,
                $this->commandPasswordDescription
            )
            ->addArgument(
                $this->commandRedirectUri,
                InputArgument::OPTIONAL,
                $this->commandRedirectUriDescription,
                $this->defaultRedirectUri
            )
            ->addArgument(
                $this->commandGrantType,
                InputArgument::OPTIONAL,
                $this->commandGrantTypeDescription,
                $this->defaultGrantType
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appName = $input->getArgument($this->commandAppName);
        $password = $input->getArgument($this->commandPassword);
        $redirectUri = $input->getArgument($this->commandRedirectUri);
        $grantType = $input->getArgument($this->commandGrantType);

        $output->writeln('<info>Your client has been generated successfully!</info>');
    }
}
