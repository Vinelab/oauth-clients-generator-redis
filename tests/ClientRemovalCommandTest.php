<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Vinelab\ClientGenerator\Commands\ClientRemovalCommand;

class ClientRemovalCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testRemovalExecute()
    {
        $application = new Application();
        $application->add(new ClientRemovalCommand());

        $command = $application->find('clients:delete');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'client_id' => 1,
        ));

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
