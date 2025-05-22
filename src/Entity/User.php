<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Voiture;
use App\Entity\Preference;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "Veuillez saisir une adresse email valide.")]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins 8 caractères.")]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/",
        message: "Le mot de passe doit contenir au moins une majuscule, une minuscule, un  caractère spécial et un chiffre."
    )]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Length(min: 10, minMessage: "Le numéro doit contenir au moins 10 chiffres.")]
    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[Assert\NotNull(message: "La date de naissance est obligatoire.")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $photo = null;

    #[Assert\NotBlank(message: "Le pseudo est obligatoire.")]
    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isSuspended = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isChauffeur = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPassager = true;


    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $note = null;

    #[ORM\Column(type: 'integer')]
    private int $credits = 20;

    #[ORM\OneToMany(mappedBy: 'conducteur', targetEntity: Avis::class)]
    private Collection $avisConducteur;



    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Voiture::class, cascade: ['persist', 'remove'])]
    private Collection $voitures;

    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'user')]
    private Collection $avis;

    /**
     * @var Collection<int, \App\Entity\Covoiturage>
     */

    #[ORM\OneToMany(mappedBy: 'conducteur', targetEntity: Covoiturage::class)]
    private Collection $covoituragesConduits;

    /**
     * @var Collection<int, \App\Entity\Covoiturage>
     */
    #[ORM\ManyToMany(targetEntity: Covoiturage::class, mappedBy: 'passagers')]
    private Collection $covoituragesEnPassager;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Preference $preference = null;

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->covoituragesConduits = new ArrayCollection();
        $this->covoituragesEnPassager = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        if ($this->photo === null) {
            return null;
        }

        // Vérifie que la ressource est encore ouverte
        if (is_resource($this->photo)) {
            rewind($this->photo); // très important pour éviter de lire à la fin
            return base64_encode(stream_get_contents($this->photo));
        }

        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function isChauffeur(): bool
    {
        return $this->isChauffeur;
    }

    public function setIsChauffeur(bool $isChauffeur): static
    {
        $this->isChauffeur = $isChauffeur;
        return $this;
    }

    public function isPassager(): bool
    {
        return $this->isPassager;
    }

    public function setIsPassager(bool $isPassager): static
    {
        $this->isPassager = $isPassager;
        return $this;
    }





    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;
        return $this;
    }



    /**
     * @return Collection<int, \App\Entity\Voiture>
     */

    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setUser($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            if ($voiture->getUser() === $this) {
                $voiture->setUser(null);
            }
        }

        return $this;
    }

    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            if ($avi->getUser() === $this) {
                $avi->setUser(null);
            }
        }

        return $this;
    }

    public function getCovoituragesConduits(): Collection
    {
        return $this->covoituragesConduits;
    }

    public function getCovoituragesEnPassager(): Collection
    {
        return $this->covoituragesEnPassager;
    }

    public function addCovoituragesEnPassager(Covoiturage $covoiturage): static
    {
        if (!$this->covoituragesEnPassager->contains($covoiturage)) {
            $this->covoituragesEnPassager->add($covoiturage);
            $covoiturage->addPassager($this);
        }

        return $this;
    }

    public function removeCovoituragesEnPassager(Covoiturage $covoiturage): static
    {
        if ($this->covoituragesEnPassager->removeElement($covoiturage)) {
            $covoiturage->removePassager($this);
        }

        return $this;
    }

    public function getPreference(): ?Preference
    {
        return $this->preference;
    }

    public function setPreference(?Preference $preference): static
    {

        if ($preference && $preference->getUser() !== $this) {
            $preference->setUser($this);
        }

        $this->preference = $preference;
        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisConducteur(): Collection
    {
        return $this->avisConducteur;
    }


    public function getNote(): ?float
    {
        return $this->note;
    }
    public function getNoteMoyenne(): ?float
    {
        $avis = $this->getAvisConducteur(); // On suppose que tu as fait la relation OneToMany "avisConducteur"

        if (count($avis) === 0) {
            return null;
        }

        $total = 0;
        foreach ($avis as $avisItem) {
            $total += $avisItem->getNote();
        }

        return round($total / count($avis), 1);
    }

    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(bool $isSuspended): static
    {
        $this->isSuspended = $isSuspended;
        return $this;
    }
}
