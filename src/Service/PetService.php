<?php

namespace App\Service;

use App\Entity\Breed;
use App\Entity\Pet;
use App\Entity\PetType;
use App\Exception\BreedNotFoundException;
use App\Exception\PetTypeNotFoundException;
use App\Exception\PetValidationException;
use App\Repository\BreedRepository;
use App\Repository\PetRepository;
use App\Repository\PetTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
        private PetTypeRepository $petTypeRepository,
        private BreedRepository $breedRepository,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * Create a new pet.
     */
    public function createPet(array $data): Pet
    {
        $pet = new Pet();
        $this->updatePetFromData($pet, $data);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $pet;
    }

    /**
     * Update pet from form data.
     */
    public function updatePetFromData(Pet $pet, array $data): void
    {
        if (isset($data['name'])) {
            $pet->setName($data['name']);
        }

        if (isset($data['type_id'])) {
            $petType = $this->petTypeRepository->find($data['type_id']);
            if ($petType) {
                $pet->setType($petType);
            }
        }

        if (isset($data['breed_id']) && $data['breed_id'] && 'cant_find' !== $data['breed_id']) {
            $breed = $this->breedRepository->find($data['breed_id']);
            if ($breed) {
                $pet->setBreed($breed);
                $pet->setCustomBreed(null);
            }
        } elseif (isset($data['breed_id']) && 'cant_find' === $data['breed_id']) {

            $pet->setBreed(null);
            if (isset($data['custom_breed_option'])) {
                if ('dont_know' === $data['custom_breed_option']) {
                    $pet->setCustomBreed("I don't know");
                } elseif ('mix' === $data['custom_breed_option']) {
                    $pet->setCustomBreed("It's a mix");
                }
            }
        }

        if (isset($data['sex'])) {
            $pet->setSex($data['sex']);
        }


        if (isset($data['knows_birth_date']) && 'yes' === $data['knows_birth_date']) {
            if (isset($data['date_of_birth'])) {
                $dateOfBirth = \DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
                if ($dateOfBirth) {
                    $pet->setDateOfBirth($dateOfBirth);
                }
            }
        } elseif (isset($data['approximate_age'])) {
            $pet->setApproximateAge((int) $data['approximate_age']);
        }


        $this->updateDangerousAnimalFlag($pet);
    }

    /**
     * Update dangerous animal flag based on breed.
     */
    private function updateDangerousAnimalFlag(Pet $pet): void
    {
        $isDangerous = false;

        if ($pet->getBreed() && $pet->getBreed()->isDangerous()) {
            $isDangerous = true;
        }

        $pet->setIsDangerousAnimal($isDangerous);
    }

    /**
     * Get all pet types.
     */
    public function getAllPetTypes(): array
    {
        return $this->petTypeRepository->findAllOrdered();
    }

    /**
     * Get breeds by pet type.
     */
    public function getBreedsByPetType(PetType $petType): array
    {
        return $this->breedRepository->findByPetType($petType);
    }

    /**
     * Get breeds by pet type ID.
     */
    public function getBreedsByPetTypeId(int $petTypeId): array
    {
        $petType = $this->petTypeRepository->find($petTypeId);
        if (!$petType) {
            return [];
        }

        return $this->getBreedsByPetType($petType);
    }

    /**
     * Get all pets.
     */
    public function getAllPets(): array
    {
        return $this->petRepository->findAllOrdered();
    }

    /**
     * Find pet by ID.
     */
    public function findPet(int $id): ?Pet
    {
        return $this->petRepository->find($id);
    }

    /**
     * Check if a breed is dangerous.
     */
    public function isBreedDangerous(int $breedId): bool
    {
        $breed = $this->breedRepository->find($breedId);

        return $breed ? $breed->isDangerous() : false;
    }

    /**
     * Get age choices for approximate age dropdown.
     */
    public function getAgeChoices(): array
    {
        $choices = [];
        for ($i = 1; $i <= 20; ++$i) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    /**
     * Get pet type by ID with exception handling.
     */
    public function getPetTypeById(int $id): PetType
    {
        $petType = $this->petTypeRepository->find($id);
        if (!$petType) {
            throw PetTypeNotFoundException::forId($id);
        }

        return $petType;
    }

    /**
     * Get breed by ID with exception handling.
     */
    public function getBreedById(int $id): Breed
    {
        $breed = $this->breedRepository->find($id);
        if (!$breed) {
            throw BreedNotFoundException::forId($id);
        }

        return $breed;
    }

    /**
     * Validate pet data.
     */
    public function validatePet(Pet $pet): void
    {
        $violations = $this->validator->validate($pet);
        if (count($violations) > 0) {
            throw PetValidationException::fromViolations($violations);
        }
    }

    /**
     * Get pet by ID with exception handling.
     */
    public function getPetById(int $id): Pet
    {
        $pet = $this->petRepository->find($id);
        if (!$pet) {
            throw new \InvalidArgumentException(sprintf('Pet with ID %d not found', $id));
        }

        return $pet;
    }
}
