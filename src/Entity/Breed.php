<?php

namespace App\Entity;

use App\Repository\BreedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BreedRepository::class)]
#[ORM\Table(name: 'breeds')]
class Breed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $isDangerous = false;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'breeds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PetType $petType = null;

    #[ORM\OneToMany(mappedBy: 'breed', targetEntity: Pet::class)]
    private Collection $pets;

    public function __construct()
    {
        $this->pets = new ArrayCollection();
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

    public function isDangerous(): bool
    {
        return $this->isDangerous;
    }

    public function setIsDangerous(bool $isDangerous): static
    {
        $this->isDangerous = $isDangerous;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPetType(): ?PetType
    {
        return $this->petType;
    }

    public function setPetType(?PetType $petType): static
    {
        $this->petType = $petType;

        return $this;
    }

    /**
     * @return Collection<int, Pet>
     */
    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function addPet(Pet $pet): static
    {
        if (!$this->pets->contains($pet)) {
            $this->pets->add($pet);
            $pet->setBreed($this);
        }

        return $this;
    }

    public function removePet(Pet $pet): static
    {
        if ($this->pets->removeElement($pet)) {
            if ($pet->getBreed() === $this) {
                $pet->setBreed(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
