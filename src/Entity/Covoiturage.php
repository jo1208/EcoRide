<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Voiture;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'covoituragesConduits')]
    private ?User $conducteur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_depart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_depart = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_depart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_arrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_arrivee = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_arrivee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $nb_place = null;

    #[ORM\Column]
    private ?float $prix_personne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;


    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'covoituragesEnPassager')]
    private Collection $passagers;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Voiture $voiture = null;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(mappedBy: 'trajet', targetEntity: Avis::class, cascade: ['persist', 'remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->passagers = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }
    public function setDateDepart(\DateTimeInterface $date_depart): static
    {
        $this->date_depart = $date_depart;
        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heure_depart;
    }
    public function setHeureDepart(\DateTimeInterface $heure_depart): static
    {
        $this->heure_depart = $heure_depart;
        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieu_depart;
    }
    public function setLieuDepart(string $lieu_depart): static
    {
        $this->lieu_depart = $lieu_depart;
        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->date_arrivee;
    }
    public function setDateArrivee(\DateTimeInterface $date_arrivee): static
    {
        $this->date_arrivee = $date_arrivee;
        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heure_arrivee;
    }
    public function setHeureArrivee(\DateTimeInterface $heure_arrivee): static
    {
        $this->heure_arrivee = $heure_arrivee;
        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieu_arrivee;
    }
    public function setLieuArrivee(string $lieu_arrivee): static
    {
        $this->lieu_arrivee = $lieu_arrivee;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }
    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nb_place;
    }
    public function setNbPlace(int $nb_place): static
    {
        $this->nb_place = $nb_place;
        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prix_personne;
    }
    public function setPrixPersonne(float $prix_personne): static
    {
        $this->prix_personne = $prix_personne;
        return $this;
    }

    public function getConducteur(): ?User
    {
        return $this->conducteur;
    }
    public function setConducteur(?User $conducteur): static
    {
        $this->conducteur = $conducteur;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPassagers(): Collection
    {
        return $this->passagers;
    }
    public function addPassager(User $user): static
    {
        if (!$this->passagers->contains($user)) {
            $this->passagers->add($user);
        }
        return $this;
    }
    public function removePassager(User $user): static
    {
        $this->passagers->removeElement($user);
        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }
    public function setVoiture(?Voiture $Voiture): static
    {
        $this->voiture = $Voiture;
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
}
