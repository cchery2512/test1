<?php

namespace App\Service;

use App\Entity\CurrencyRate;
use App\Resource\ExchangeRatesResource;

class CurrencyRatesService
{
    /**
     * @param array|string $values
     * @return array
     */
    public function validateValues($values): array
    {
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

    /**
     * @param string $value
     * @return array
     */
    public function validateSingleValue($value): array
    {
        $status = true;
        $message = '';
        // Verificar que el valor sea una cadena de longitud 3
        if (strlen($value) !== 3 || !ctype_upper($value)) {
            if(strlen($value) === 0){
                $message .= 'Not enough arguments (missing: "target_currencies").';
            }else{
                if (strlen($value) !== 3) {
                    $message .= 'All values ​​must be strings of length 3.';
                }
    
                // Verificar que el valor esté compuesto únicamente por letras mayúsculas
                if (!ctype_upper($value)) {
                    $message .= 'All values ​​must be uppercase letters.';
                }
            }

            $status = false;
        }
        return [
            'message'   => $message,
            'status'    => $status
        ];
    }

    /**
     * @param array $value
     * @return array
     */
    public function formatData(array $currencies): array
    {
        return array_map(function (CurrencyRate $currency) {
            return ExchangeRatesResource::format($currency);
        }, $currencies);
    }
}
