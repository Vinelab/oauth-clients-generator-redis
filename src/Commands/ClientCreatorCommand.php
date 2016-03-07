<?php

namespace Vinelab\ClientGenerator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCreatorCommand extends Command
{
    protected $commandName = 'clients:create';

    protected $commandDescription = 'Generates a new client.';

    protected $commandAppName = 'name';

    protected $commandAppDescription = 'Name of the app.';

    protected $commandRedirectUri = 'redirect_uri';

    protected $commandRedirectUriDescription = 'Redirect Uri.';

    protected $defaultRedirectUri = '';

    protected $commandGrantType = 'grantType';

    protected $defaultGrantType = 'client_credentials';

    protected $commandGrantTypeDescription = 'The grant type to use with OAuth2.';

    protected $commandPassword = 'password';

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
