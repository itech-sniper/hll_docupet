<?php

namespace App\Repository;

use App\Entity\Breed;
use App\Entity\PetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Breed>
 */
class BreedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Breed::class);
    }

    public function findByPetType(PetType $petType): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.petType = :petType')
            ->setParameter('petType', $petType)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
