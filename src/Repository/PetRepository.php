<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pet>
 */
class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * Find all pets ordered by creation date (newest first)
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'pt')
            ->leftJoin('p.breed', 'b')
            ->addSelect('pt', 'b')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find dangerous pets
     */
    public function findDangerous(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'pt')
            ->leftJoin('p.breed', 'b')
            ->addSelect('pt', 'b')
            ->where('p.isDangerousAnimal = :dangerous')
            ->setParameter('dangerous', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pets by type
     */
    public function findByType(string $typeName): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'pt')
            ->leftJoin('p.breed', 'b')
            ->addSelect('pt', 'b')
            ->where('LOWER(pt.name) = LOWER(:typeName)')
            ->setParameter('typeName', $typeName)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
