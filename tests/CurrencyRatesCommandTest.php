<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

class CurrencyRatesCommandTest extends ApiTestCase
{
    public function testCommandInvalid(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        //Case 1: Command without parameters
        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => '',
            'target_currencies' => ''
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Not enough arguments (missing: "base_currency, target_currencies").', $output);

        //Case 2: Command without parameter: target_currencies (The first value will always be assumed to correspond to the parameter: 'base_currency')
        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => 'EUR',
            'target_currencies' => ''
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Not enough arguments (missing: "target_currencies").', $output);

        //Case 3: Incorrect 'base_currency' parameter
        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => 'EU',
            'target_currencies' => ['USD','GBP']
        ]);
        
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('All values ​​must be strings of length 3. Value => EU', $output);
        
        /**
         *  Case 4: The command validated the parameters correctly but the API query was successful but did not return values ​​(usually happens if the base_currency parameter does not exist) 
        */
        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => 'EUX',
            'target_currencies' => ['USD','GBP']
        ]);
        
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Error when querying the API', $output);

        //Case 5: The command validated the parameters correctly but the queried API does not return the target_currencies values 

        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => 'EUR',
            'target_currencies' => ['USA','GBX']
        ]);
        
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The API did not return target_currencies for the entered base_currency, please check your inputs.', $output);

        //Finally validate if the status code is 2 => Command::INVALID
        $this->assertEquals(2, $commandTester->getStatusCode());
    }

    public function testCommandIsSuccessful(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency' => 'EUR',
            'target_currencies' => ['USD','GBP']
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Currency exchange rates obtained and stored correctly in the database and Redis.', $output);
    }
}
