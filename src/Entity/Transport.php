<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $desription = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?bool $disponibilte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDesription(): ?string
    {
        return $this->desription;
    }

    public function setDesription(string $desription): static
    {
        $this->desription = $desription;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function isDisponibilte(): ?bool
    {
        return $this->disponibilte;
    }

    public function setDisponibilte(bool $disponibilte): static
    {
        $this->disponibilte = $disponibilte;

        return $this;
    }
}
