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
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reclamationsEnvoyees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $auteur = null; // Celui qui fait la réclamation

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reclamationsRecues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $cible = null; // Celui contre qui la réclamation est faite

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
    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): self { $this->auteur = $auteur; return $this; }

    public function getCible(): ?User { return $this->cible; }
    public function setCible(?User $cible): self { $this->cible = $cible; return $this; }
  
    public function __construct()
    {
        $this->dateSoumission = new \DateTime(); // Définit automatiquement la date
    }
}
