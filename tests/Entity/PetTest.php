<?php

namespace App\Tests\Entity;

use App\Entity\Pet;
use App\Entity\PetType;
use App\Entity\Breed;
use PHPUnit\Framework\TestCase;

class PetTest extends TestCase
{
    public function testPetCreation(): void
    {
        $pet = new Pet();
        $pet->setName('Buddy');
        $pet->setSex(Pet::SEX_MALE);

        $this->assertEquals('Buddy', $pet->getName());
        $this->assertEquals(Pet::SEX_MALE, $pet->getSex());
        $this->assertInstanceOf(\DateTimeInterface::class, $pet->getCreatedAt());
    }

    public function testAgeCalculationFromDateOfBirth(): void
    {
        $pet = new Pet();
        $dateOfBirth = new \DateTime('2020-01-01');
        $pet->setDateOfBirth($dateOfBirth);

        $expectedAge = (new \DateTime())->diff($dateOfBirth)->y;
        $this->assertEquals($expectedAge, $pet->getAge());
    }

    public function testApproximateAge(): void
    {
        $pet = new Pet();
        $pet->setApproximateAge(5);

        $this->assertEquals(5, $pet->getAge());
        $this->assertEquals(5, $pet->getApproximateAge());
        $this->assertNull($pet->getDateOfBirth());
    }

    public function testDateOfBirthClearsApproximateAge(): void
    {
        $pet = new Pet();
        $pet->setApproximateAge(5);
        $pet->setDateOfBirth(new \DateTime('2020-01-01'));

        $this->assertNull($pet->getApproximateAge());
        $this->assertInstanceOf(\DateTimeInterface::class, $pet->getDateOfBirth());
    }

    public function testApproximateAgeClearsDateOfBirth(): void
    {
        $pet = new Pet();
        $pet->setDateOfBirth(new \DateTime('2020-01-01'));
        $pet->setApproximateAge(5);

        $this->assertNull($pet->getDateOfBirth());
        $this->assertEquals(5, $pet->getApproximateAge());
    }

    public function testDangerousBreedSetsFlag(): void
    {
        $pet = new Pet();
        $petType = new PetType();
        $petType->setName('Dog');

        $dangerousBreed = new Breed();
        $dangerousBreed->setName('Pitbull');
        $dangerousBreed->setIsDangerous(true);
        $dangerousBreed->setPetType($petType);

        $pet->setBreed($dangerousBreed);

        $this->assertTrue($pet->isDangerousAnimal());
    }

    public function testSafeBreedDoesNotSetFlag(): void
    {
        $pet = new Pet();
        $petType = new PetType();
        $petType->setName('Dog');

        $safeBreed = new Breed();
        $safeBreed->setName('Labrador');
        $safeBreed->setIsDangerous(false);
        $safeBreed->setPetType($petType);

        $pet->setBreed($safeBreed);

        $this->assertFalse($pet->isDangerousAnimal());
    }

    public function testCustomBreedName(): void
    {
        $pet = new Pet();
        $pet->setCustomBreed('Mixed breed');

        $this->assertEquals('Mixed breed', $pet->getBreedName());
        $this->assertEquals('Mixed breed', $pet->getCustomBreed());
    }

    public function testBreedNameFromBreedEntity(): void
    {
        $pet = new Pet();
        $petType = new PetType();
        $petType->setName('Dog');

        $breed = new Breed();
        $breed->setName('Golden Retriever');
        $breed->setPetType($petType);

        $pet->setBreed($breed);

        $this->assertEquals('Golden Retriever', $pet->getBreedName());
    }

    public function testSexChoices(): void
    {
        $choices = Pet::getSexChoices();

        $this->assertIsArray($choices);
        $this->assertArrayHasKey('Male', $choices);
        $this->assertArrayHasKey('Female', $choices);
        $this->assertArrayHasKey('Unknown', $choices);
        $this->assertEquals(Pet::SEX_MALE, $choices['Male']);
        $this->assertEquals(Pet::SEX_FEMALE, $choices['Female']);
        $this->assertEquals(Pet::SEX_UNKNOWN, $choices['Unknown']);
    }
}
