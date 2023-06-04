<?php

namespace App\Resource;

use App\Entity\CurrencyRate;

class ExchangeRatesResource{
    public static function format(CurrencyRate $currencyRate): ?array{
        return [
            'id' => $currencyRate->getId(),
            'baseCurrency' => $currencyRate->getBaseCurrency(),
            'targetCurrency' => $currencyRate->getTargetCurrency(),
            'rate' => $currencyRate->getRate(),
        ];
    }
}