<?php

namespace App\Controller;

use App\Cache\ExchangeRatesCache;
use App\Repository\CurrencyRateRepository;
use App\Request\ExchangeRatesRequest;
use App\Service\CurrencyRatesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ExchangeRatesController extends AbstractController
{

    public function __construct(private CurrencyRateRepository $currencyRateRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function index(
        Request $request,
        ExchangeRatesRequest $exchangeRatesRequest,
        ExchangeRatesCache $exchangesRatesCache,
        CurrencyRatesService $service

    ): Response {
        try {
            $validatedData = $exchangeRatesRequest->validated($request);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(["message" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $currencies     = $exchangesRatesCache->findByParams($validatedData, intval($this->getParameter('app.ttl_cache')));
        
        if(count($currencies['data']) == 0){
            return new JsonResponse(["message" => 'The API did not return target_currencies for the entered base_currency, please check your inputs.'], Response::HTTP_BAD_REQUEST);
        }else{
            $formattedData  = $service->formatData($currencies['data']);
            return new JsonResponse([
                'data' => $formattedData,
                'data_resource' => $currencies['data_source']
            ]);
        }
    }
}
