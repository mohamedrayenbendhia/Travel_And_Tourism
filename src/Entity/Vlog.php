<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\VlogRepository")]
#[ORM\Table(name: "vlog")]
class Vlog
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu ne peut pas être vide.')]
    private string $content;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'vlogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    // Constructeur
    public function __construct()
    {
        $this->createdAt = new \DateTime(); // Initialisation de createdAt lors de la création du Vlog
    }

    // Getter et Setter pour 'id'
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
{
    $this->author = $author;
    return $this;
}

    // Getter et Setter pour 'content'
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    // Getter et Setter pour 'image'
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    // Getter et Setter pour 'video'
    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;
        return $this;
    }

    // Getter et Setter pour 'createdAt'
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
