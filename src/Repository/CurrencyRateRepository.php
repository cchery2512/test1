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

    /*public function find(): array{
        return $this->createQueryBuilder();
    }*/

//    /**
//     * @return CurrencyRate[] Returns an array of CurrencyRate objects
//     */
//    public function findByExampleField($value): array{
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CurrencyRate
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
