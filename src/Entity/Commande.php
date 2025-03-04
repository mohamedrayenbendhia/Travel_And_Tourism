<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null; // Change from Collection to a single User
    
    // /**
    //  * @var Collection<int, Menu>
    //  */
    // #[ORM\OneToMany(targetEntity: Menu::class, mappedBy: 'commande')]
    // private Collection $menu_id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_commande = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    
    #[ORM\ManyToOne(inversedBy: 'commandeRestau')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    public function __construct()
    {
        $this->utilisateur_id = new ArrayCollection();
        // $this->menu_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }
    
    public function setUtilisateur(User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }


    public function addUtilisateurId(user $utilisateurId): static
    {
        if (!$this->utilisateur_id->contains($utilisateurId)) {
            $this->utilisateur_id->add($utilisateurId);
            $utilisateurId->setCommande($this);
        }

        return $this;
    }

    public function removeUtilisateurId(user $utilisateurId): static
    {
        if ($this->utilisateur_id->removeElement($utilisateurId)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurId->getCommande() === $this) {
                $utilisateurId->setCommande(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Menu>
    //  */
    // public function getMenuId(): Collection
    // {
    //     return $this->menu_id;
    // }

    // public function addMenuId(Menu $menuId): static
    // {
    //     if (!$this->menu_id->contains($menuId)) {
    //         $this->menu_id->add($menuId);
    //         $menuId->setCommande($this);
    //     }

    //     return $this;
    // }

    // public function removeMenuId(Menu $menuId): static
    // {
    //     if ($this->menu_id->removeElement($menuId)) {
    //         // set the owning side to null (unless already changed)
    //         if ($menuId->getCommande() === $this) {
    //             $menuId->setCommande(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getRestaurantId(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant; // FIX: Change from $this->restaurant_id to $this->restaurant
        return $this;
    }
    


}
