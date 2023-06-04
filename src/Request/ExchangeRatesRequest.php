<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

class ExchangeRatesRequest{


    public function validated(Request $request): array{
        $baseCurrency = $request->query->get('base_currency');
        $targetCurrencies = $request->query->get('target_currencies');

        if (!$baseCurrency) {
            throw new \InvalidArgumentException('Missing base_currency parameter: base_currency');
        }

        if (!$targetCurrencies) {
            throw new \InvalidArgumentException('Missing target_currencies parameter: target_currencies');
        }

        $requestData = [
            'base_currency' => $baseCurrency,
            'target_currencies' => explode(',', $targetCurrencies),
        ];

        $this->validateBaseCurrency($requestData['base_currency']);
        $this->validateTargetCurrencies($requestData['target_currencies']);

        return $requestData;
    }

    private function validateBaseCurrency(?string $baseCurrency): void
    {
        $constraints = new Assert\Collection([
            'base_currency' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(3),
                new Assert\Regex([
                    'pattern' => '/^[A-Z]+$/',
                    'message' => 'The base_currency must consist of 3 uppercase letters.',
                ]),
            ],
        ]);

        $this->validate($baseCurrency, $constraints, 'base_currency');
    }

    private function validateTargetCurrencies(?array $targetCurrencies): void
    {
        $constraints = new Assert\Collection([
            'target_currencies' => [
                new Assert\NotBlank(),
                new Assert\All([
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Length(3),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]+$/',
                        'message' => 'The target_currency must consist of 3 uppercase letters.',
                    ]),
                ]),
            ],
        ]);

        $this->validate($targetCurrencies, $constraints, 'target_currencies');
    }

    private function validate($value, Assert\Collection $constraints, string $field): void
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate([$field => $value], $constraints);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }

            throw new \InvalidArgumentException(implode(' ', $messages));
        }
    }
}