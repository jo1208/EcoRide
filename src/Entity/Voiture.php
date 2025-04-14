<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(length: 255)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 255)]
    private ?string $energie = null;

    #[ORM\Column(length: 255)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_premiere_immatriculation = null;

    #[ORM\ManyToOne(inversedBy: 'Voiture')]
    private ?User $user = null;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'Voiture')]
    private Collection $covoiturages;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getEnergie(): ?string
    {
        return $this->energie;
    }

    public function setEnergie(string $energie): static
    {
        $this->energie = $energie;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDatePremiereImmatriculation(): ?\DateTimeInterface
    {
        return $this->date_premiere_immatriculation;
    }

    public function setDatePremiereImmatriculation(\DateTimeInterface $date_premiere_immatriculation): static
    {
        $this->date_premiere_immatriculation = $date_premiere_immatriculation;

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

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setVoiture($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            // set the owning side to null (unless already changed)
            if ($covoiturage->getVoiture() === $this) {
                $covoiturage->setVoiture(null);
            }
        }

        return $this;
    }
    #[ORM\Column(type: 'boolean')]
    private ?bool $ecologique = false;

    public function isEcologique(): ?bool
    {
        return $this->ecologique;
    }

    public function setEcologique(bool $ecologique): static
    {
        $this->ecologique = $ecologique;
        return $this;
    }
}
