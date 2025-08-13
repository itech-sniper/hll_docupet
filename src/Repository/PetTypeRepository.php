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

    /**
     * Find all pet types ordered by name
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('pt')
            ->orderBy('pt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pet type by name (case insensitive)
     */
    public function findByName(string $name): ?PetType
    {
        return $this->createQueryBuilder('pt')
            ->where('LOWER(pt.name) = LOWER(:name)')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
