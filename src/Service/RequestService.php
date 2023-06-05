<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;

class RequestService
{
    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }
    /**
     * @param string $baseCurrency
     * @param array $targetCurrencies
     * @return string
     */
    function makeHttpRequest(string $baseCurrency, array $targetCurrencies)
    {
        $url = $this->params->get('app.open_exchange_rates_url');
        $appId = $this->params->get('app.open_exchange_rates_app_id');

        $symbols = implode(',', $targetCurrencies);

        $queryParams = http_build_query([
            'app_id' => $appId,
            'base' => $baseCurrency,
            'symbols' => $symbols,
        ]);

        $apiUrl = $url . '?' . $queryParams;

        $client = new Client();

        try {
            $response = $client->get($apiUrl);
            $body = $response->getBody()->getContents();

            return $body;
        } catch (GuzzleException $e) {
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
