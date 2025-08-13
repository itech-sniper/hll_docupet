<?php

namespace App\DataFixtures;

use App\Entity\PetType;
use App\Entity\Breed;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PetTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create Pet Types
        $dogType = new PetType();
        $dogType->setName('Dog');
        $dogType->setDescription('Domestic dog breeds');
        $manager->persist($dogType);

        $catType = new PetType();
        $catType->setName('Cat');
        $catType->setDescription('Domestic cat breeds');
        $manager->persist($catType);

        // Dog Breeds
        $dogBreeds = [
            // Safe breeds
            ['name' => 'Labrador Retriever', 'dangerous' => false],
            ['name' => 'Golden Retriever', 'dangerous' => false],
            ['name' => 'German Shepherd', 'dangerous' => false],
            ['name' => 'Bulldog', 'dangerous' => false],
            ['name' => 'Poodle', 'dangerous' => false],
            ['name' => 'Beagle', 'dangerous' => false],
            ['name' => 'Rottweiler', 'dangerous' => false],
            ['name' => 'Yorkshire Terrier', 'dangerous' => false],
            ['name' => 'Dachshund', 'dangerous' => false],
            ['name' => 'Siberian Husky', 'dangerous' => false],
            ['name' => 'Boxer', 'dangerous' => false],
            ['name' => 'Border Collie', 'dangerous' => false],
            
            // Dangerous breeds (as specified in requirements)
            ['name' => 'Pitbull', 'dangerous' => true],
            ['name' => 'Mastiff', 'dangerous' => true],
            
            // Additional dangerous breeds for completeness
            ['name' => 'American Staffordshire Terrier', 'dangerous' => true],
            ['name' => 'Doberman Pinscher', 'dangerous' => true],
        ];

        foreach ($dogBreeds as $breedData) {
            $breed = new Breed();
            $breed->setName($breedData['name']);
            $breed->setIsDangerous($breedData['dangerous']);
            $breed->setPetType($dogType);
            $manager->persist($breed);
        }

        // Cat Breeds
        $catBreeds = [
            ['name' => 'Persian', 'dangerous' => false],
            ['name' => 'Maine Coon', 'dangerous' => false],
            ['name' => 'British Shorthair', 'dangerous' => false],
            ['name' => 'Ragdoll', 'dangerous' => false],
            ['name' => 'Bengal', 'dangerous' => false],
            ['name' => 'Abyssinian', 'dangerous' => false],
            ['name' => 'Birman', 'dangerous' => false],
            ['name' => 'Oriental Shorthair', 'dangerous' => false],
            ['name' => 'American Shorthair', 'dangerous' => false],
            ['name' => 'Scottish Fold', 'dangerous' => false],
            ['name' => 'Sphynx', 'dangerous' => false],
            ['name' => 'Russian Blue', 'dangerous' => false],
            ['name' => 'Siamese', 'dangerous' => false],
            ['name' => 'Norwegian Forest Cat', 'dangerous' => false],
            ['name' => 'Exotic Shorthair', 'dangerous' => false],
        ];

        foreach ($catBreeds as $breedData) {
            $breed = new Breed();
            $breed->setName($breedData['name']);
            $breed->setIsDangerous($breedData['dangerous']);
            $breed->setPetType($catType);
            $manager->persist($breed);
        }

        $manager->flush();
    }
}
