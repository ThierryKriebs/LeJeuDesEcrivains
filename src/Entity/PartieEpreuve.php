<?php

namespace App\Entity;

use App\Repository\PartieEpreuveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieEpreuveRepository::class)]
class PartieEpreuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $num_etape = null;

    #[ORM\ManyToOne(inversedBy: 'partieEpreuves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SousCategorieEtape $sous_categorie = null;

    #[ORM\ManyToOne(inversedBy: 'partieEpreuves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partie $partie = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebutEpreuve = null;

    #[ORM\ManyToOne(inversedBy: 'partieEpreuves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EpreuveEtat $etatEpreuve = null;

    /**
     * @var Collection<int, Redaction>
     */
    #[ORM\OneToMany(targetEntity: Redaction::class, mappedBy: 'partieEpreuve')]
    private Collection $redactions;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateFinEpreuve = null;

    public function __construct()
    {
        $this->redactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumEtape(): ?int
    {
        return $this->num_etape;
    }

    public function setNumEtape(int $num_etape): static
    {
        $this->num_etape = $num_etape;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorieEtape
    {
        return $this->sous_categorie;
    }

    public function setSousCategorie(?SousCategorieEtape $sous_categorie): static
    {
        $this->sous_categorie = $sous_categorie;

        return $this;
    }

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): static
    {
        $this->partie = $partie;

        return $this;
    }

    public function getDateDebutEpreuve(): ?\DateTimeImmutable
    {
        return $this->dateDebutEpreuve;
    }

    public function setDateDebutEpreuve(\DateTimeImmutable $dateDebutEpreuve): static
    {
        $this->dateDebutEpreuve = $dateDebutEpreuve;

        return $this;
    }

    public function getEtatEpreuve(): ?EpreuveEtat
    {
        return $this->etatEpreuve;
    }

    public function setEtatEpreuve(?EpreuveEtat $etatEpreuve): static
    {
        $this->etatEpreuve = $etatEpreuve;

        return $this;
    }

    /**
     * @return Collection<int, Redaction>
     */
    public function getRedactions(): Collection
    {
        return $this->redactions;
    }

    public function addRedaction(Redaction $redaction): static
    {
        if (!$this->redactions->contains($redaction)) {
            $this->redactions->add($redaction);
            $redaction->setPartieEpreuve($this);
        }

        return $this;
    }

    public function removeRedaction(Redaction $redaction): static
    {
        if ($this->redactions->removeElement($redaction)) {
            // set the owning side to null (unless already changed)
            if ($redaction->getPartieEpreuve() === $this) {
                $redaction->setPartieEpreuve(null);
            }
        }

        return $this;
    }

    public function getDateFinEpreuve(): ?\DateTimeImmutable
    {
        return $this->dateFinEpreuve;
    }

    public function setDateFinEpreuve(?\DateTimeImmutable $dateFinEpreuve): static
    {
        $this->dateFinEpreuve = $dateFinEpreuve;

        return $this;
    }
}
