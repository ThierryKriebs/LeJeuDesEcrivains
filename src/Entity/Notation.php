<?php

namespace App\Entity;

use App\Repository\NotationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotationRepository::class)]
class Notation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?redaction $redaction = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?utilisateurs $noteur = null;

    #[ORM\Column]
    //Empêche l'envoi au controller de valeurs en dehors de la plage définie
    #[Assert\Range(
        min: 0,
        max: 20,
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}'
    )]
    
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRedaction(): ?redaction
    {
        return $this->redaction;
    }

    public function setRedaction(?redaction $redaction): static
    {
        $this->redaction = $redaction;

        return $this;
    }

    public function getNoteur(): ?utilisateurs
    {
        return $this->noteur;
    }

    public function setNoteur(?utilisateurs $noteur): static
    {
        $this->noteur = $noteur;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): static
    {
        $this->remarque = $remarque;

        return $this;
    }
}
