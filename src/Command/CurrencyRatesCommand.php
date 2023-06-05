<?php

namespace App\Command;

use App\Cache\ExchangeRatesCache;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Request\ExchangeRatesRequest;
use App\Resource\ExchangeRatesResource;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:currency:rates',
    description: 'Fetches currency exchange rates from Open Exchange Rates API and stores them in MySQL and Redis.',
    hidden: false,
)]
class CurrencyRatesCommand extends Command{
    private $currencyRateRepository;
    private $exchangesRatesCache;
    public function __construct(private CurrencyRateRepository $currencyRateRepo, ExchangeRatesCache $exchangesRatesCach){
        $this->currencyRateRepository = $currencyRateRepo;
        $this->exchangesRatesCache = $exchangesRatesCach;
        parent::__construct();
    }
    
    protected function configure(): void{
        $this->addArgument('base_currency', InputArgument::REQUIRED, 'Base currency')
        ->addArgument('target_currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Target currencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $io = new SymfonyStyle($input, $output);

        $status = $this->validateValues($input->getArgument('base_currency'));
        if($status['status'] == false){
            $io->error($status['message'].' Value => '. $input->getArgument('base_currency'));
            return Command::INVALID;
        }
        $status = $this->validateValues($input->getArgument('target_currencies'));
        if($status['status'] == false){
            $io->error($status['message'].' Value => '. $input->getArgument('target_currencies'));
            return Command::INVALID;
        }

        $response = $this->makeHttpRequest($input->getArgument('base_currency'), $input->getArgument('target_currencies'));
        
        $response = (array)json_decode($response);

        //dd($response);

        $result =$this->currencyRateRepository->updateOrCreate($response);

        $currencies     = $this->exchangesRatesCache->findByParams($result, intval($_ENV["TTL_CACHE"]));
        $formattedData  = array_map(fn(CurrencyRate $currency) => ExchangeRatesResource::format($currency), $currencies['data']);
        
        $datos = new JsonResponse([
            'data' => $formattedData,
            'data_resource' => $currencies['data_source']
        ]);

        $io->success($datos);
        // if($cadena->getStatusCode() === 200){
        //     $cadena = $cadena->json();
        //     //$cadena = json_encode($cadena, true);
        //     //$this->currencyRateRepository->updateOrCreate($cadena);
        //     //dd(gettype($cadena));
        //     $io->success($cadena);
        // }else{
        //     $io->error($cadena->getStatusCode());
        //     return Command::INVALID;
        // }
        return Command::SUCCESS;
    }


    function validateValues($values): array {
        if (is_array($values)) {
            foreach ($values as $value) {
                $result = $this->validateSingleValue($value);
                if ($result['status'] == false) {
                    return $result;
                }
            }
            return [
                'message'   => '',
                'status'    => true
            ];
        } else {
            return $this->validateSingleValue($values);
        }
    }
    
    function validateSingleValue($value): array{
        $status = true;
        $message = '';
        // Verificar que el valor sea una cadena de longitud 3
        if(strlen($value) !== 3 || !ctype_upper($value)){
            if (strlen($value) !== 3) {
                $message .= 'Todos los valores deben ser cadenas de longitud 3.';
            }
        
            // Verificar que el valor esté compuesto únicamente por letras mayúsculas
            if (!ctype_upper($value)) {
                $message .= 'Todos los valores deben ser letras mayúsculas.';
            }
            $status = false;
        }
        return [
            'message'   => $message,
            'status'    => $status
        ];
    }


    function makeHttpRequest(string $baseCurrency, array $targetCurrencies) {
        $url = 'https://openexchangerates.org/api/latest.json';
        $appId = '0fb6b31eeb2d4ed5a7281011d6ca9837';
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
            //$statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            return $body;
            /*if ($statusCode === 200) {
                return new Response($body);
            } else {
                return new Response($body, $statusCode);
            }*/
        } catch (GuzzleException $e) {
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
}