<?php

namespace App\Cache;

use App\Repository\CurrencyRateRepository;
use App\Service\RequestService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ExchangeRatesCache{
    private RequestService $requestService;

    public function __construct(
        private CacheInterface $cache,
        private CurrencyRateRepository $currencyRateRepository,
        RequestService $requestServic
    ){
        $this->requestService = $requestServic;
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
        
        if(count($data) == 0){
            $this->cache->delete($cacheKey);
            $value = $this->callToApi($validatedData);
            return $value == true ? $this->findByParams($validatedData, $ttl_cache) : ['data' => []];

        }else{
            return [
                'data' => $data,
                'data_source' => $isHit
            ];
        }
    }

    public function callToApi(array $validatedData):bool {
        $response = $this->requestService
        ->makeHttpRequest(
            $validatedData['base_currency'],
            $validatedData['target_currencies']
        );
    
        if($response != ''){
            $response = (array) json_decode($response);
            if(count((array)$response['rates']) > 0){
                $this->currencyRateRepository->updateOrCreate($response);
                return true;
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
}
