<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[UniqueEntity(fields: ['login'], message: 'Ce compte utilisateur existe déjà')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà associée à un autre compte utilisateur')]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["getJoueurs"])]
    private ?string $login = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, PartieJoueur>
     */
    #[ORM\OneToMany(targetEntity: PartieJoueur::class, mappedBy: 'Joueur')]
    private Collection $partieJoueurs;

    /**
     * @var Collection<int, Redaction>
     */
    #[ORM\OneToMany(targetEntity: Redaction::class, mappedBy: 'joueur')]
    private Collection $redactions;

    /**
     * @var Collection<int, Notation>
     */
    #[ORM\OneToMany(targetEntity: Notation::class, mappedBy: 'noteur')]
    private Collection $notations;

    public function __construct()
    {
        $this->partieJoueurs = new ArrayCollection();
        $this->redactions = new ArrayCollection();
        $this->notations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

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
            $partieJoueur->setJoueur($this);
        }

        return $this;
    }

    public function removePartieJoueur(PartieJoueur $partieJoueur): static
    {
        if ($this->partieJoueurs->removeElement($partieJoueur)) {
            // set the owning side to null (unless already changed)
            if ($partieJoueur->getJoueur() === $this) {
                $partieJoueur->setJoueur(null);
            }
        }

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
            $redaction->setJoueur($this);
        }

        return $this;
    }

    public function removeRedaction(Redaction $redaction): static
    {
        if ($this->redactions->removeElement($redaction)) {
            // set the owning side to null (unless already changed)
            if ($redaction->getJoueur() === $this) {
                $redaction->setJoueur(null);
            }
        }

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
            $notation->setNoteur($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): static
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getNoteur() === $this) {
                $notation->setNoteur(null);
            }
        }

        return $this;
    }
}
