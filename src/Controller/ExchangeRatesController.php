<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRatesController extends AbstractController{
    #[Route('/exchange-rates', name: 'exchange_rates')]
    public function index(): JsonResponse{
        return $this->json([
            'message' => 'Hello World!',
            'path' => 'src/Controller/ExchangeRatesController.php',
        ]);
    }
}
