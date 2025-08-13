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

    /**
     * Find breeds by pet type, ordered by name
     */
    public function findByPetType(PetType $petType): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.petType = :petType')
            ->setParameter('petType', $petType)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find dangerous breeds by pet type
     */
    public function findDangerousByPetType(PetType $petType): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.petType = :petType')
            ->andWhere('b.isDangerous = :dangerous')
            ->setParameter('petType', $petType)
            ->setParameter('dangerous', true)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find breed by name and pet type (case insensitive)
     */
    public function findByNameAndPetType(string $name, PetType $petType): ?Breed
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.name) = LOWER(:name)')
            ->andWhere('b.petType = :petType')
            ->setParameter('name', $name)
            ->setParameter('petType', $petType)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
