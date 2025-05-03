<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
  
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code_connexion = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LongueurPartie $longueur_partie = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GenreLitteraire $genre_litteraire = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartieEtat $etat = null;

    

    /**
     * @var Collection<int, PartieJoueur>
     */
    #[ORM\OneToMany(targetEntity: PartieJoueur::class, mappedBy: 'Partie')]
    private Collection $partieJoueurs;

    /**
     * @var Collection<int, PartieEpreuve>
     */
    #[ORM\OneToMany(targetEntity: PartieEpreuve::class, mappedBy: 'partie')]
    private Collection $partieEpreuves;

    public function __construct()
    {
        $this->partieEpreuves = new ArrayCollection();
        $this->partieJoueurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeConnexion(): ?string
    {
        return $this->code_connexion;
    }

    public function getCode_connexion(): ?string
    {
        return $this->code_connexion;
    }

    public function setCodeConnexion(string $code_connexion): static
    {
        $this->code_connexion = $code_connexion;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeImmutable $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getLongueurPartie(): ?LongueurPartie
    {
        return $this->longueur_partie;
    }

    public function setLongueurPartie(?LongueurPartie $longueur_partie): static
    {
        $this->longueur_partie = $longueur_partie;

        return $this;
    }

    public function getGenreLitteraire(): ?GenreLitteraire
    {
        return $this->genre_litteraire;
    }

    public function setGenreLitteraire(?GenreLitteraire $genre_litteraire): static
    {
        $this->genre_litteraire = $genre_litteraire;

        return $this;
    }

    public function getEtat(): ?PartieEtat
    {
        return $this->etat;
    }

    public function setEtat(?PartieEtat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, PartieEpreuve>
     */
    public function getPartieEpreuves(): Collection
    {
        return $this->partieEpreuves;
    }

    public function addPartieEpreufe(PartieEpreuve $partieEpreufe): static
    {
        if (!$this->partieEpreuves->contains($partieEpreufe)) {
            $this->partieEpreuves->add($partieEpreufe);
            $partieEpreufe->setPartie($this);
        }

        return $this;
    }

    public function removePartieEpreufe(PartieEpreuve $partieEpreufe): static
    {
        if ($this->partieEpreuves->removeElement($partieEpreufe)) {
            // set the owning side to null (unless already changed)
            if ($partieEpreufe->getIdPartie() === $this) {
                $partieEpreufe->setIdPartie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PartieJoueur>
     */
    public function getPartieJoueurs(): Collection
    {
        return $this->partieJoueurs;
    }

    public function addPartieJoueur(PartieJoueur $partieJoueur): static
    {
        if (!$this->partieJoueurs->contains($partieJoueur)) {
            $this->partieJoueurs->add($partieJoueur);
            $partieJoueur->setPartie($this);
        }

        return $this;
    }

    public function removePartieJoueur(PartieJoueur $partieJoueur): static
    {
        if ($this->partieJoueurs->removeElement($partieJoueur)) {
            // set the owning side to null (unless already changed)
            if ($partieJoueur->getPartie() === $this) {
                $partieJoueur->setPartie(null);
            }
        }

        return $this;
    }
}
