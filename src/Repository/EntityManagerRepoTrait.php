<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @method EntityManagerInterface getEntityManager()
 */
trait EntityManagerRepoTrait
{
    public function save($entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager-> persist($entity);
        $entityManager->flush();

    }

    public function remove($entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }
}
