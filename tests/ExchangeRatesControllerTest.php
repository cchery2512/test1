<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ExchangeRatesControllerTest extends ApiTestCase
{

    //Testing input errors
    public function testInputErrors(): void{

        //First case: lowercase in param 'base_currency'
        static::createClient()->request('GET', '/api/exchange-rates?base_currency=EUr&target_currencies=USD');

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains(['message' => 'The base_currency must consist of 3 uppercase letters.']);

        //Second case: lowercase in element of param 'target_currencies'
        static::createClient()->request('GET', '/api/exchange-rates?base_currency=EUR&target_currencies=USd');

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains(['message' => 'The target_currency must consist of 3 uppercase letters.']);

        //Third case: missing param 'base_currency'
        static::createClient()->request('GET', '/api/exchange-rates?base_currenc=EUR&target_currencies=USD');

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains(['message' => 'Missing parameter: base_currency']);

        //Third case: missing param 'target_currencies'
        static::createClient()->request('GET', '/api/exchange-rates?base_currency=EUR&target_currencie=USD');

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains(['message' => 'Missing parameter: target_currencies']);

    } 

    //Testing query response with results
    public function testQueryWithResults(): void
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

        $this->assertResponseHeaderSame('content-type', 'application/json');
        
    }
    
    //Testing query response with no results
    public function testQueryWithOutResults(): void
    {
        $response = static::createClient();
        $response->request('GET', '/api/exchange-rates?base_currency=EUA&target_currencies=USD');
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "data" => [],
            "data_resource" => "mysql"
        ]);
    }

}
