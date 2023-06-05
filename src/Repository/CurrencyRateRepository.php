<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 *
 * @method CurrencyRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRate[]    findAll()
 * @method CurrencyRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateRepository extends ServiceEntityRepository{

    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, CurrencyRate::class);
    }

    public function save(CurrencyRate $entity, bool $flush = false): void{
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CurrencyRate $entity, bool $flush = false): void{
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return CurrencyRate[] Returns an array of CurrencyRate objects
     */
    public function findByParams($param): ?array{
        return $this->createQueryBuilder('cr')
        ->andWhere('cr.baseCurrency = :baseCurrency')
        ->setParameter('baseCurrency', $param['base_currency'])
        ->andWhere('cr.targetCurrency IN (:targetCurrencies)')
        ->setParameter('targetCurrencies', $param['target_currencies'])
        ->getQuery()
        ->getResult();
    }

    public function updateOrCreate($data){
        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        $targetCurrencies = [];
        try {
            // Find an existing instance of CurrencyRate
            foreach ($data['rates'] as $key => $value) {
                array_push($targetCurrencies, $key);
                $currencyRate = $this->findOneBy([
                    'baseCurrency' => $data['base'],
                    'targetCurrency' => $key,
                ]);
    
                if (!$currencyRate) {
                    // Create a new instance of CurrencyRate if it doesn't exist
                    $currencyRate = new CurrencyRate();
                    $currencyRate->setBaseCurrency($data['base']);
                    $currencyRate->setTargetCurrency($key);
                    $currencyRate->setRate($value);
                }else{
                    // If it exists in the database, update the values
                    $currencyRate->setBaseCurrency($data['base']);
                    $currencyRate->setTargetCurrency($key);
                    $currencyRate->setRate($value);
                }
    
                // Persist the changes
                $entityManager->persist($currencyRate);
                $entityManager->flush();
            }

            $entityManager->commit();
            $data['base_currency'] = $data['base'];
            $data['target_currencies'] = $targetCurrencies;
            return $data;
        }catch (\Exception $e) {
            $entityManager->rollback();
            throw $e;
        }
    }
}