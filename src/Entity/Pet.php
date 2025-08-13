<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PetRepository::class)]
#[ORM\Table(name: 'pets')]
class Pet
{
    public const SEX_MALE = 'male';
    public const SEX_FEMALE = 'female';
    public const SEX_UNKNOWN = 'unknown';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'pets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PetType $type = null;

    #[ORM\ManyToOne(inversedBy: 'pets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Breed $breed = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 50)]
    private ?int $approximateAge = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::SEX_MALE, self::SEX_FEMALE, self::SEX_UNKNOWN])]
    private ?string $sex = null;

    #[ORM\Column]
    private bool $isDangerousAnimal = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customBreed = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?PetType
    {
        return $this->type;
    }

    public function setType(?PetType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getBreed(): ?Breed
    {
        return $this->breed;
    }

    public function setBreed(?Breed $breed): static
    {
        $this->breed = $breed;
        
        // Automatically set dangerous animal flag based on breed
        if ($breed && $breed->isDangerous()) {
            $this->isDangerousAnimal = true;
        }
        
        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        
        // Clear approximate age if date of birth is set
        if ($dateOfBirth) {
            $this->approximateAge = null;
        }
        
        return $this;
    }

    public function getApproximateAge(): ?int
    {
        return $this->approximateAge;
    }

    public function setApproximateAge(?int $approximateAge): static
    {
        $this->approximateAge = $approximateAge;
        
        // Clear date of birth if approximate age is set
        if ($approximateAge !== null) {
            $this->dateOfBirth = null;
        }
        
        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;
        return $this;
    }

    public function isDangerousAnimal(): bool
    {
        return $this->isDangerousAnimal;
    }

    public function setIsDangerousAnimal(bool $isDangerousAnimal): static
    {
        $this->isDangerousAnimal = $isDangerousAnimal;
        return $this;
    }

    public function getCustomBreed(): ?string
    {
        return $this->customBreed;
    }

    public function setCustomBreed(?string $customBreed): static
    {
        $this->customBreed = $customBreed;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Calculate age based on date of birth or return approximate age
     */
    public function getAge(): ?int
    {
        if ($this->dateOfBirth) {
            $now = new \DateTime();
            return $now->diff($this->dateOfBirth)->y;
        }
        
        return $this->approximateAge;
    }

    /**
     * Get the display name for the breed (custom or from breed entity)
     */
    public function getBreedName(): ?string
    {
        if ($this->customBreed) {
            return $this->customBreed;
        }
        
        return $this->breed?->getName();
    }

    public static function getSexChoices(): array
    {
        return [
            'Male' => self::SEX_MALE,
            'Female' => self::SEX_FEMALE,
            'Unknown' => self::SEX_UNKNOWN,
        ];
    }
}
