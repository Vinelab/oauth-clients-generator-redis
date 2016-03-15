<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Vinelab\ClientGenerator\Commands\ClientCreatorCommand;

class ClientCreatorCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationExecute()
    {
        $application = new Application();
        $application->add(new ClientCreatorCommand());

        $command = $application->find('clients:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'Android',
            'password' => 'pass',
        ));

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
