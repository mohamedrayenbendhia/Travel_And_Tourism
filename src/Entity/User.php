<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.")]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: "Le nom d'utilisateur ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $username = null;

    // Définition de la relation OneToMany avec EvaluationReclamation
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EvaluationReclamation::class)]
    private Collection $evaluations;
    #[ORM\OneToMany(mappedBy: "auteur", targetEntity: Reclamation::class, orphanRemoval: true)]
    private Collection $reclamationsEnvoyees;
    
    #[ORM\OneToMany(mappedBy: "cible", targetEntity: Reclamation::class)]
    private Collection $reclamationsRecues;
    public function __construct()
    {
        // Initialiser la collection d'évaluations
        $this->evaluations = new ArrayCollection();
        $this->reclamationsEnvoyees = new ArrayCollection();
        $this->reclamationsRecues = new ArrayCollection();
    }
    public function getReclamationsEnvoyees(): Collection
    {
        return $this->reclamationsEnvoyees;
    }
     
    public function getReclamationsRecues(): Collection
    {
        return $this->reclamationsRecues;
    }
    // Getter et Setter pour evaluations
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(EvaluationReclamation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setUser($this);
        }

        return $this;
    }

    public function removeEvaluation(EvaluationReclamation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // On met à null la relation dans EvaluationReclamation
            if ($evaluation->getUser() === $this) {
                $evaluation->setUser(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getRoles(): array
    {
        // Ajoute automatiquement "ROLE_USER" s'il n'est pas déjà présent
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function setRoles(array $roles): static
    {
        // Assure que tous les rôles commencent par "ROLE_"
        $this->roles = array_unique(array_map(
            fn($role) => str_starts_with($role, 'ROLE_') ? $role : 'ROLE_' . strtoupper($role),
            $roles
        ));
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si besoin, on peut ajouter ici un nettoyage des données sensibles
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;
        return $this;
    }
}
