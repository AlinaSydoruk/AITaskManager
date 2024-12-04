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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function createEmployeesQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');

    }

    public function findAll(): array
    {
        $employees = parent::findAll();
        if ($employees) {
            foreach ($employees as $employee) {
                $this->updateWorkingStatus($employee);
            }
        }
        return $employees;
    }

    public function find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null):object|null
    {
        $employee = parent::find($id, $lockMode, $lockVersion);

        if ($employee instanceof Employee) {
            $this->updateWorkingStatus($employee);
        }
        return $employee;
    }


    public function save($entity): void
    {
        if (!$this->updateWorkingStatus($entity)) {
            $entityManager = $this->getEntityManager();
            $entityManager-> persist($entity);
            $entityManager->flush();
        }

    }

    public function remove($entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }

    private function updateWorkingStatus(Employee $employee): bool
    {
        $currentDate = new \DateTime();
        $workingStatus = $employee->getWorkStatus();

        if ($employee->getLastWorkingDay() <= $currentDate) {
            $employee->setWorkStatus(WorkStatus::dismissed);

        } elseif ($employee->getFirstWorkingDay() > $currentDate) {
            $employee->setWorkStatus(WorkStatus::notYetStartedWorking);
        } else {
            $employee->setWorkStatus(WorkStatus::working);
        }

        if ($employee->getWorkStatus() !== $workingStatus) {
            $this->save($employee);
            return true;
        }
        return false;
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

//    public function findOneBySomeField($value): ?Employee
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
