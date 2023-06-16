<?php

namespace App\Repository;

use App\Entity\Additional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Additional>
 *
 * @method Additional|null find($id, $lockMode = null, $lockVersion = null)
 * @method Additional|null findOneBy(array $criteria, array $orderBy = null)
 * @method Additional[]    findAll()
 * @method Additional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Additional::class);
    }

    public function save(Additional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Additional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function deleteAll(){
        $query = $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
        return $query;
    }
//    /**
//     * @return Additional[] Returns an array of Additional objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Additional
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
