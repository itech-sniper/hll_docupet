<?php

namespace App\Repository;

use App\Entity\PetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetType>
 */
class PetTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetType::class);
    }

    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('pt')
            ->orderBy('pt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
