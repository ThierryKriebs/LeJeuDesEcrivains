<?php

namespace App\Entity;

use App\Repository\GenreLitteraireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreLitteraireRepository::class)]
class GenreLitteraire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Commentaire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $exemple = null;

    #[ORM\Column]
    private ?bool $est_active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_image = null;

    /**
     * @var Collection<int, Partie>
     */
    #[ORM\OneToMany(targetEntity: Partie::class, mappedBy: 'genre_litteraire')]
    private Collection $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(?string $Commentaire): static
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    public function getExemple(): ?string
    {
        return $this->exemple;
    }

    public function setExemple(?string $exemple): static
    {
        $this->exemple = $exemple;

        return $this;
    }
  

    public function estActive(): ?bool
    {
        return $this->est_active;
    }

    public function setEstActive(bool $est_active): static
    {
        $this->est_active = $est_active;

        return $this;
    }

    //utilisé par Easyadmin...
    public function getNomImage(): ?string
    {
        return $this->nom_image;
    }

    //Double pour le fichier Twig....
    public function getNom_Image(): ?string
    {
        return $this->nom_image;
    }

    public function setNomImage(string $nom_image): static
    {
        $this->nom_image = $nom_image;

        return $this;
    }

    /**
     * @return Collection<int, Partie>
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Partie $party): static
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
            $party->setGenreLitteraire($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): static
    {
        if ($this->parties->removeElement($party)) {
            // set the owning side to null (unless already changed)
            if ($party->getGenreLitteraire() === $this) {
                $party->setGenreLitteraire(null);
            }
        }

        return $this;
    }


    public function __toString()
    {
        return $this->getNom();
    }
}
