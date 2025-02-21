<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\OneToOne(mappedBy: 'restaurant_id', cascade: ['persist', 'remove'])]
    private ?Menu $menu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        // unset the owning side of the relation if necessary
        if ($menu === null && $this->menu !== null) {
            $this->menu->setRestaurantId(null);
        }

        // set the owning side of the relation if necessary
        if ($menu !== null && $menu->getRestaurantId() !== $this) {
            $menu->setRestaurantId($this);
        }

        $this->menu = $menu;

        return $this;
    }
}
