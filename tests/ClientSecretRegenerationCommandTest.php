<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Vinelab\ClientGenerator\Commands\ClientSecretRegenerationCommand;

class ClientSecretRegenerationCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testSecretRegenerationExecute()
    {
        $application = new Application();
        $application->add(new ClientSecretRegenerationCommand());

        $command = $application->find('clients:regenerate-secret');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'client_id' => 1,
            'password' => 'pass',
        ));

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
