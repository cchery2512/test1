<?php

namespace App\Command;

use App\Cache\ExchangeRatesCache;
use App\Repository\CurrencyRateRepository;
use App\Service\CurrencyRatesService;
use App\Service\RequestService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsCommand(
    name: 'app:currency:rates',
    description: 'Fetches currency exchange rates from Open Exchange Rates API and stores them in MySQL and Redis.',
    hidden: false,
)]
class CurrencyRatesCommand extends Command
{
    private CurrencyRateRepository $currencyRateRepository;
    private ExchangeRatesCache $exchangesRatesCache;
    private CurrencyRatesService $currencyRatesService;
    private RequestService $requestService;

    public function __construct(
        CurrencyRateRepository $currencyRateRepo,
        ExchangeRatesCache $exchangesRatesCach,
        CurrencyRatesService $currencyRatesServic,
        RequestService $requestServic
    ) {
        $this->currencyRateRepository = $currencyRateRepo;
        $this->exchangesRatesCache = $exchangesRatesCach;
        $this->currencyRatesService = $currencyRatesServic;
        $this->requestService = $requestServic;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('base_currency', InputArgument::REQUIRED, 'Base currency')
            ->addArgument('target_currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Target currencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$status = $this->currencyRatesService->validateValues($input->getArgument('target_currencies'));
        if (!$input->getArgument('base_currency') && !$input->getArgument('target_currencies')) {
            $io->error('Not enough arguments (missing: "base_currency, target_currencies").');
            return Command::INVALID;
        }

        $status = $this->currencyRatesService->validateValues($input->getArgument('base_currency'));
        if ($status['status'] == false) {
            $io->error($status['message'] . ' Value => ' . $input->getArgument('base_currency'));
            return Command::INVALID;
        }
        $status = $this->currencyRatesService->validateValues($input->getArgument('target_currencies'));
        if ($status['status'] == false) {
            $io->error($status['message'] . ' Value => ' . $input->getArgument('target_currencies'));
            return Command::INVALID;
        }

 

        $response = $this->requestService
            ->makeHttpRequest(
                $input->getArgument('base_currency'),
                $input->getArgument('target_currencies')
            );
        
        if($response !== ''){
            $response = (array) json_decode($response);
            if(json_encode($response['rates']) == '{}'){
                $io->error('The API did not return target_currencies for the entered base_currency, please check your inputs.');
                return Command::INVALID;
            }
        }else{
            $io->error('Error when querying the API');
            return Command::INVALID;
        }

        $result = $this->currencyRateRepository->updateOrCreate($response);

        $currencies     = $this->exchangesRatesCache->findByParams($result, intval($_ENV["TTL_CACHE"]));

        //$formattedData  = $this->currencyRatesService->formatData($currencies['data']);

        /*$datos = new JsonResponse([
            'data' => $formattedData,
            'data_resource' => $currencies['data_source']
        ]);*/
        $io->success('Currency exchange rates obtained and stored correctly in the database and Redis.');
        return Command::SUCCESS;
    }
}
