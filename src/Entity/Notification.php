<?php
// src/Entity/Notification.php
// src/Entity/Notification.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notification
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    private string $message;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user; // L'Hôte qui recevra la notification

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreated;

    public function __construct(string $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
        $this->dateCreated = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }
}
