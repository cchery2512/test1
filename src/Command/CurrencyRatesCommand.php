<?php

namespace App\Command;

use App\Request\ExchangeRatesRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:currency:rates',
    description: 'Fetches currency exchange rates from Open Exchange Rates API and stores them in MySQL and Redis.',
    hidden: false,
)]
class CurrencyRatesCommand extends Command{
    protected function configure(): void{
        $this
        //->setName('app:currency:rates')
        //->setDescription('Fetches currency exchange rates from Open Exchange Rates API and stores them in MySQL and Redis.')
        ->addArgument('base_currency', InputArgument::REQUIRED, 'Base currency')
        ->addArgument('target_currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Target currencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $io = new SymfonyStyle($input, $output);

        $status = $this->validateValues($input->getArgument('base_currency'));
        if($status['status'] == false) $io->error($status['message']. $input->getArgument('base_currency'));
        $status = $this->validateValues($input->getArgument('target_currencies'));
        if($status['status'] == false) $io->error($status['message']. $input->getArgument('base_currency'));
        /*$arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }*/
        $io->error('Currency exchange rates fetched and stored successfully.'. $input);
        //$io->fail('Currency exchange rates fetched and stored successfully.'. gettype($input->getArgument('target_currencies')));
        return Command::SUCCESS;
        /*$baseCurrency = $input->getArgument('base_currency');
        $targetCurrencies = $input->getArgument('target_currencies');

        // Obtener los tipos de cambio de la API de Open Exchange Rates
        $exchangeRates = $this->fetchExchangeRates($baseCurrency, $targetCurrencies);

        // Guardar los tipos de cambio en la base de datos MySQL
        $this->saveToMySQL($exchangeRates);

        // Guardar los tipos de cambio en Redis
        $this->saveToRedis($exchangeRates);*/

       // $output->writeln('Currency exchange rates fetched and stored successfully.');

        //return Command::SUCCESS;
    }


    function validateValues($values): array {
        if (is_array($values)) {
            foreach ($values as $value) {
                $result = $this->validateSingleValue($value);
                if ($result['status'] == false) {
                    return $result;
                }
            }
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
    
}
