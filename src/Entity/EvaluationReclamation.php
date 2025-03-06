<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: 'App\Repository\EvaluationReclamationRepository')]
class EvaluationReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $note = null;

    #[ORM\Column(type: 'text')]
    private ?string $commentaire = null;

    // Définir la relation ManyToOne avec User
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]  // Cette colonne est obligatoire
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $reclamation = null;

    // Getter et setter pour user
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter et setter pour reclamation
    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    // Getter et Setter pour note et commentaire
    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
