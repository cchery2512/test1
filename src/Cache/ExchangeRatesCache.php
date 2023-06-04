<?php

namespace App\Cache;

use App\Repository\CurrencyRateRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ExchangeRatesCache{
    public function __construct(private CacheInterface $cache, private CurrencyRateRepository $currencyRateRepository){
        
    }

    public function findByParams(array $validatedData): ?array{
        $cacheKey       = "find-by-params-" . md5(json_encode($validatedData));
        $isHit = $this->cache->getItem($cacheKey)->isHit() ? 'redis/cache' : 'mysql';

        $data = $this->cache->get($cacheKey, function(ItemInterface $item) use($validatedData){
            $item->expiresAfter(10);
            //var_dump('Cache expired: the database was queried');
            return $this->currencyRateRepository->findByParams($validatedData);
        });
        return [
            'data' => $data,
            'data_source' => $isHit
        ];
    }
}
