<?php

// src/Entity/Reclamation.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Reclamation
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $photo;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $document;

    #[ORM\Column(type: "string")]
    private string $categorie;

    #[ORM\Column(type: "string")]
    private string $statut = 'en cours'; // "en cours", "résolue", "refusée"

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateSoumission;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user; // Voyageur

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $host; // Hôte

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateSoumission(): \DateTimeInterface
    {
        return $this->dateSoumission;
    }

    public function setDateSoumission(\DateTimeInterface $dateSoumission): self
    {
        $this->dateSoumission = $dateSoumission;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getHost(): User
    {
        return $this->host;
    }

    public function setHost(User $host): self
    {
        $this->host = $host;
        return $this;
    }
}
