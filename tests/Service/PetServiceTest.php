<?php

namespace App\Tests\Service;

use App\Entity\Pet;
use App\Entity\PetType;
use App\Entity\Breed;
use App\Service\PetService;
use App\Repository\PetRepository;
use App\Repository\PetTypeRepository;
use App\Repository\BreedRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PetServiceTest extends TestCase
{
    private PetService $petService;
    private MockObject $entityManager;
    private MockObject $petRepository;
    private MockObject $petTypeRepository;
    private MockObject $breedRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->petTypeRepository = $this->createMock(PetTypeRepository::class);
        $this->breedRepository = $this->createMock(BreedRepository::class);

        $this->petService = new PetService(
            $this->entityManager,
            $this->petRepository,
            $this->petTypeRepository,
            $this->breedRepository
        );
    }

    public function testCreatePetWithBasicData(): void
    {
        $data = [
            'name' => 'Buddy',
            'type_id' => 1,
            'sex' => Pet::SEX_MALE,
            'approximate_age' => 3
        ];

        $petType = new PetType();
        $petType->setName('Dog');

        $this->petTypeRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($petType);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Pet::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $pet = $this->petService->createPet($data);

        $this->assertInstanceOf(Pet::class, $pet);
        $this->assertEquals('Buddy', $pet->getName());
        $this->assertEquals(Pet::SEX_MALE, $pet->getSex());
        $this->assertEquals(3, $pet->getApproximateAge());
        $this->assertEquals($petType, $pet->getType());
    }

    public function testCreatePetWithDangerousBreed(): void
    {
        $data = [
            'name' => 'Rex',
            'type_id' => 1,
            'breed_id' => 1,
            'sex' => Pet::SEX_MALE,
            'approximate_age' => 2
        ];

        $petType = new PetType();
        $petType->setName('Dog');

        $dangerousBreed = new Breed();
        $dangerousBreed->setName('Pitbull');
        $dangerousBreed->setIsDangerous(true);
        $dangerousBreed->setPetType($petType);

        $this->petTypeRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($petType);

        $this->breedRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($dangerousBreed);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Pet::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $pet = $this->petService->createPet($data);

        $this->assertTrue($pet->isDangerousAnimal());
        $this->assertEquals($dangerousBreed, $pet->getBreed());
    }

    public function testCreatePetWithCustomBreed(): void
    {
        $data = [
            'name' => 'Fluffy',
            'type_id' => 1,
            'breed_id' => 'cant_find',
            'custom_breed_option' => 'mix',
            'sex' => Pet::SEX_FEMALE,
            'approximate_age' => 1
        ];

        $petType = new PetType();
        $petType->setName('Cat');

        $this->petTypeRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($petType);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Pet::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $pet = $this->petService->createPet($data);

        $this->assertEquals("It's a mix", $pet->getCustomBreed());
        $this->assertNull($pet->getBreed());
    }

    public function testCreatePetWithDateOfBirth(): void
    {
        $data = [
            'name' => 'Charlie',
            'type_id' => 1,
            'sex' => Pet::SEX_MALE,
            'knows_birth_date' => 'yes',
            'date_of_birth' => '2020-05-15'
        ];

        $petType = new PetType();
        $petType->setName('Dog');

        $this->petTypeRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($petType);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Pet::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $pet = $this->petService->createPet($data);

        $this->assertInstanceOf(\DateTimeInterface::class, $pet->getDateOfBirth());
        $this->assertEquals('2020-05-15', $pet->getDateOfBirth()->format('Y-m-d'));
        $this->assertNull($pet->getApproximateAge());
    }

    public function testGetAgeChoices(): void
    {
        $choices = $this->petService->getAgeChoices();

        $this->assertIsArray($choices);
        $this->assertCount(20, $choices);
        $this->assertEquals(1, $choices[1]);
        $this->assertEquals(20, $choices[20]);
    }

    public function testIsBreedDangerous(): void
    {
        $dangerousBreed = new Breed();
        $dangerousBreed->setIsDangerous(true);

        $this->breedRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($dangerousBreed);

        $result = $this->petService->isBreedDangerous(1);

        $this->assertTrue($result);
    }

    public function testIsBreedDangerousWithNonExistentBreed(): void
    {
        $this->breedRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->petService->isBreedDangerous(999);

        $this->assertFalse($result);
    }
}
