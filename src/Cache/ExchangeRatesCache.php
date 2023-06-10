<?php

namespace App\Cache;

use App\Repository\CurrencyRateRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ExchangeRatesCache{
    public function __construct(private CacheInterface $cache, private CurrencyRateRepository $currencyRateRepository){
        
    }

    public function findByParams(array $validatedData, int $ttl_cache): ?array{
        $array = (array) $validatedData['target_currencies'];
        sort($array);
        $stringFormatValidatedData = "base_currency=" . $validatedData['base_currency'] . "_target_currencies=" . implode(',', $array);
        $cacheKey       = "find-by-params-" . $stringFormatValidatedData;
        
        $isHit = $this->cache->getItem($cacheKey)->isHit() ? 'redis/cache' : 'mysql';

        $data = $this->cache->get($cacheKey, function(ItemInterface $item) use($validatedData, $ttl_cache){
            $item->expiresAfter($ttl_cache);
            //var_dump('Cache expired: the database was queried');
            return $this->currencyRateRepository->findByParams($validatedData);
        });

        return [
            'data' => $data,
            'data_source' => $isHit
        ];
    }
}
