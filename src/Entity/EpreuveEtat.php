<?php

namespace App\Entity;

use App\Repository\EpreuveEtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveEtatRepository::class)]
class EpreuveEtat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    /**
     * @var Collection<int, PartieEpreuve>
     */
    #[ORM\OneToMany(targetEntity: PartieEpreuve::class, mappedBy: 'etatEpreuve')]
    private Collection $partieEpreuves;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

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
            $partieEpreufe->setEtatEpreuve($this);
        }

        return $this;
    }

    public function removePartieEpreufe(PartieEpreuve $partieEpreufe): static
    {
        if ($this->partieEpreuves->removeElement($partieEpreufe)) {
            // set the owning side to null (unless already changed)
            if ($partieEpreufe->getEtatEpreuve() === $this) {
                $partieEpreufe->setEtatEpreuve(null);
            }
        }

        return $this;
    }
}
