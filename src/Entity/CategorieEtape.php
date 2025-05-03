<?php

namespace App\Entity;

use App\Repository\CategorieEtapeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieEtapeRepository::class)]
class CategorieEtape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /**
     * @var Collection<int, SousCategorieEtape>
     */
    #[ORM\OneToMany(targetEntity: SousCategorieEtape::class, mappedBy: 'categorieEtape')]
    private Collection $sousCategorieEtapes;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $explication = null;

    public function __construct()
    {
        $this->sousCategorieEtapes = new ArrayCollection();
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

    /**
     * @return Collection<int, SousCategorieEtape>
     */
    public function getSousCategorieEtapes(): Collection
    {
        return $this->sousCategorieEtapes;
    }

    public function addSousCategorieEtape(SousCategorieEtape $sousCategorieEtape): static
    {
        if (!$this->sousCategorieEtapes->contains($sousCategorieEtape)) {
            $this->sousCategorieEtapes->add($sousCategorieEtape);
            $sousCategorieEtape->setIdCategorie($this);
        }

        return $this;
    }

    public function removeSousCategorieEtape(SousCategorieEtape $sousCategorieEtape): static
    {
        if ($this->sousCategorieEtapes->removeElement($sousCategorieEtape)) {
            // set the owning side to null (unless already changed)
            if ($sousCategorieEtape->getIdCategorie() === $this) {
                $sousCategorieEtape->setIdCategorie(null);
            }
        }

        return $this;
    }

    public function getExplication(): ?string
    {
        return $this->explication;
    }

    public function setExplication(?string $explication): static
    {
        $this->explication = $explication;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
