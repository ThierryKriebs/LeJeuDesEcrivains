<?php

namespace App\Entity;

use App\Repository\RedactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RedactionRepository::class)]
class Redaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'redactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartieEpreuve $partieEpreuve = null;

    #[ORM\ManyToOne(inversedBy: 'redactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $joueur = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $redaction = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\Column(nullable: true)]
    private ?int $classement = null;


    /**
     * @var Collection<int, Notation>
     */
    #[ORM\OneToMany(targetEntity: Notation::class, mappedBy: 'redaction')]
    private Collection $notations;

    public function __construct()
    {
        $this->notations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartieEpreuve(): ?PartieEpreuve
    {
        return $this->partieEpreuve;
    }

    public function setPartieEpreuve(?PartieEpreuve $partieEpreuve): static
    {
        $this->partieEpreuve = $partieEpreuve;

        return $this;
    }

    public function getJoueur(): ?Utilisateurs
    {
        return $this->joueur;
    }

    public function setJoueur(?Utilisateurs $joueur): static
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getRedaction(): ?string
    {
        return $this->redaction;
    }

    public function setRedaction(string $redaction): static
    {
        $this->redaction = $redaction;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getClassement(): ?int
    {
        return $this->classement;
    }

    public function setClassement(?int $classement): static
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * @return Collection<int, Notation>
     */
    public function getNotations(): Collection
    {
        return $this->notations;
    }

    public function addNotation(Notation $notation): static
    {
        if (!$this->notations->contains($notation)) {
            $this->notations->add($notation);
            $notation->setRedaction($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): static
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getRedaction() === $this) {
                $notation->setRedaction(null);
            }
        }

        return $this;
    }

    //Que doit faire Symfony quand un formulaire demande un string d'un objet de type Redaction.
    public function __toString(): string
    {
        if (!is_null($this->redaction))
        {
            return $this->redaction;
        }

        else
        {
            return "";
        }
        
    }
}
