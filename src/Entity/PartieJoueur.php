<?php

namespace App\Entity;

use App\Repository\PartieJoueurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PartieJoueurRepository::class)]
class PartieJoueur
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'partieJoueurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partie $Partie = null;

    #[ORM\ManyToOne(inversedBy: 'partieJoueurs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getJoueurs"])]
    private ?Utilisateurs $Joueur = null;

    #[ORM\Column]
    #[Groups(["getJoueurs"])]
    private ?bool $estCreateur = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\Column(nullable: true)]
    private ?int $classement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartie(): ?Partie
    {
        return $this->Partie;
    }

    public function setPartie(?Partie $Partie): static
    {
        $this->Partie = $Partie;

        return $this;
    }

    public function getJoueur(): ?Utilisateurs
    {
        return $this->Joueur;
    }

    public function setJoueur(?Utilisateurs $Joueur): static
    {
        $this->Joueur = $Joueur;

        return $this;
    }

    public function isEstCreateur(): ?bool
    {
        return $this->estCreateur;
    }

    public function setEstCreateur(bool $estCreateur): static
    {
        $this->estCreateur = $estCreateur;

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
}
