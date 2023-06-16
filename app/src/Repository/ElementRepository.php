<?php

namespace App\Repository;

use App\Entity\Element;
use App\Entity\Additional;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Element>
 *
 * @method Element|null find($id, $lockMode = null, $lockVersion = null)
 * @method Element|null findOneBy(array $criteria, array $orderBy = null)
 * @method Element[]    findAll()
 * @method Element[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Element::class);
    }

    public function save(Element $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Element $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getFullData(){
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.elementId, e.name, e.price, e.availability, e.description, e.additional, e.quantity, e.currency')
            ->addSelect('a.photoUrl')
            ->leftJoin(Additional::class, 'a', Join::WITH, 'e.elementId = a.element_id');

        $query = $qb->getQuery();

        $result = $query->getResult();
        return $result;
    }

    public function deleteAll(){
        $query = $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
        return $query;
    }

//    /**
//     * @return Element[] Returns an array of Element objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Element
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
