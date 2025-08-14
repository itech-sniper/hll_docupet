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


}
