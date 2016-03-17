<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Vinelab\ClientGenerator\Commands\ClientGetterCommand;

class ClientGetterCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterExecute()
    {
        $application = new Application();
        $application->add(new ClientGetterCommand());

        $command = $application->find('clients:fetch');
        $commandTester = new CommandTester($command);
        // $commandTester->execute(array(
        //     'command' => $command->getName(),
        //     'client_id' => 1,
        // ));

        // $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
