<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ExchangeRatesControllerTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/api/exchange-rates?base_currency=EUR&target_currencies=USD');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "data" => [
                [
                    'baseCurrency' => 'EUR',
                    'targetCurrency' => 'USD',
                    'rate' => 1.07,
                ]
                ],
                "data_resource" => "mysql"
        ]);
        
    }
}
