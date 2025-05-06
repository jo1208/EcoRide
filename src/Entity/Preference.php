<?php

namespace App\Entity;

use App\Repository\PreferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
class Preference
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fumeur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $animal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $musique = null;

    #[ORM\OneToOne(inversedBy: 'preference', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $autres = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(?bool $fumeur): static
    {
        $this->fumeur = $fumeur;
        return $this;
    }

    public function isAnimal(): ?bool
    {
        return $this->animal;
    }

    public function setAnimal(?bool $animal): static
    {
        $this->animal = $animal;
        return $this;
    }

    public function isMusique(): ?bool
    {
        return $this->musique;
    }

    public function setMusique(?bool $musique): static
    {
        $this->musique = $musique;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getAutres(): ?string
    {
        return $this->autres;
    }

    public function setAutres(?string $autres): static
    {
        $this->autres = $autres;
        return $this;
    }
}
