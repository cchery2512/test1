<?php

namespace App\Controller;

use App\Cache\ExchangeRatesCache;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Request\ExchangeRatesRequest;
use App\Resource\ExchangeRatesResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ExchangeRatesController extends AbstractController{

    public function __construct(private CurrencyRateRepository $currencyRateRepository, private EntityManagerInterface $entityManager){}

    public function index(Request $request, ExchangeRatesRequest $exchangeRatesRequest, ExchangeRatesCache $exchangesRatesCache): Response {
        try {
            $validatedData = $exchangeRatesRequest->validated($request);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $currencies     = $exchangesRatesCache->findByParams($validatedData, intval($this->getParameter('app.ttl_cache')));
        $formattedData  = array_map(fn(CurrencyRate $currency) => ExchangeRatesResource::format($currency), $currencies['data']);
        
        return new JsonResponse([
            'data' => $formattedData,
            'data_resource' => $currencies['data_source']
        ]);

    }
}
