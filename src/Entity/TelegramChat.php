<?php

namespace App\Entity;

use App\Repository\TelegramChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TelegramChatRepository::class)]
class TelegramChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id ;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'telegramChats')]
    private Collection $users;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->users = new ArrayCollection();
    }

    public function getId(): int { return $this->id; }

    public function getUsers(): Collection
    { return $this->users; }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
