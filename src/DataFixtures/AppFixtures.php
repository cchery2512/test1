<?php

namespace App\DataFixtures;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $currencyRate = new CurrencyRate();
        $currencyRate->setBaseCurrency('EUR');
        $currencyRate->setTargetCurrency('USD');
        $currencyRate->setRate(1.07);
        $manager->persist($currencyRate);
        
        $currencyRate = new CurrencyRate();
        $currencyRate->setBaseCurrency('EUR');
        $currencyRate->setTargetCurrency('GBP');
        $currencyRate->setRate(0.860764);
        $manager->persist($currencyRate);

        $manager->flush();
    }
}
