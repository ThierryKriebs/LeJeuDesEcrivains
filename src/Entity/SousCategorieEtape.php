<?php

namespace App\Entity;

use App\Repository\SousCategorieEtapeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: SousCategorieEtapeRepository::class)]
class SousCategorieEtape 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $duree_par_defaut = null;

    #[ORM\ManyToOne(inversedBy: 'sousCategorieEtapes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieEtape $categorieEtape = null;

    /**
     * @var Collection<int, PartieEpreuve>
     */
    #[ORM\OneToMany(targetEntity: PartieEpreuve::class, mappedBy: 'sous_categorie')]
    private Collection $partieEpreuves;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $explication = null;

    public function __construct()
    {
        $this->partieEpreuves = new ArrayCollection();
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

    public function getDureeParDefaut(): ?int
    {
        return $this->duree_par_defaut;
    }

    public function setDureeParDefaut(int $duree_par_defaut): static
    {
        $this->duree_par_defaut = $duree_par_defaut;

        return $this;
    }

    public function getCategorieEtape(): ?CategorieEtape
    {
        return $this->categorieEtape;
    }

    public function setCategorieEtape(?CategorieEtape $id_categorie): static
    {
        $this->categorieEtape = $id_categorie;

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
            $partieEpreufe->setIdSousCategorie($this);
        }

        return $this;
    }

    public function removePartieEpreufe(PartieEpreuve $partieEpreufe): static
    {
        if ($this->partieEpreuves->removeElement($partieEpreufe)) {
            // set the owning side to null (unless already changed)
            if ($partieEpreufe->getIdSousCategorie() === $this) {
                $partieEpreufe->setIdSousCategorie(null);
            }
        }

        return $this;
    }

    public function getExplication(): ?string
    {
        return $this->explication;
    }

    public function setExplication(string $explication): static
    {
        $this->explication = $explication;

        return $this;
    }
}
