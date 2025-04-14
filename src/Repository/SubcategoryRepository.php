<?php

namespace App\Repository;

use App\Entity\Board;
use App\Entity\Subcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subcategory>
 */
class SubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subcategory::class);
    }


    public function findWithChildren(int $id): ?Subcategory
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.children', 'c')
            ->addSelect('c')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRootByBoard(Board $board): ?Subcategory
    {
        return $this->createQueryBuilder('s')
            ->where('s.board = :board')
            ->andWhere('s.parent IS NULL')
            ->setParameter('board', $board)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Subcategory[] Returns an array of Subcategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subcategory
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
