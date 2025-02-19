<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\WorkStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\LockMode;

/**
 * @extends ServiceEntityRepository<Employee>
 * @method EntityManagerInterface getEntityManager()
 */
class EmployeeRepository extends ServiceEntityRepository
{
    use EntityManagerRepoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function createEmployeesQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');

    }

    public function findEmployeeByEmail($value): ?Employee
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


//    /**
//     * @return Employee[] Returns an array of Employee objects
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


}
