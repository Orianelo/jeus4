<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 * @UniqueEntity("email")
 */
class Player implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_inscription;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_co;

    /**
     * @ORM\Column(type="boolean")
     */
    private $connexion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocage;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="json_array")
     */
    private $friend;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="j1")
     */
    private $game1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="j2")
     */
    private $game2;

    /**
     * @ORM\Column(type="integer")
     */
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Chat", mappedBy="destinataire")
     */
    private $chat;

    public function __construct()
    {
        $this->date_co = new \DateTime();
        $this->connexion = false;
        $this->blocage = false;
        $this->points = 0;
        $this->notification = 0;
        $this->date_inscription = new \DateTime();
        $this->friend = [];
        $this->game1 = new ArrayCollection();
        $this->game2 = new ArrayCollection();
        $this->chat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getDateCo(): ?\DateTimeInterface
    {
        return $this->date_co;
    }

    public function setDateCo(\DateTimeInterface $date_co): self
    {
        $this->date_co = $date_co;

        return $this;
    }

    public function getConnexion(): ?bool
    {
        return $this->connexion;
    }

    public function setConnexion(bool $connexion): self
    {
        $this->connexion = $connexion;

        return $this;
    }

    public function getBlocage(): ?bool
    {
        return $this->blocage;
    }

    public function setBlocage(bool $blocage): self
    {
        $this->blocage = $blocage;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @param mixed $friend
     */
    public function setFriend($friend): void
    {
        $this->friend = $friend;
    }


    /**
     * @return Collection|Game[]
     */
    public function getGame1(): Collection
    {
        return $this->game1;
    }

    public function addGame1(Game $game1): self
    {
        if (!$this->game1->contains($game1)) {
            $this->game1[] = $game1;
            $game1->setJ1($this);
        }

        return $this;
    }

    public function removeGame1(Game $game1): self
    {
        if ($this->game1->contains($game1)) {
            $this->game1->removeElement($game1);
            // set the owning side to null (unless already changed)
            if ($game1->getJ1() === $this) {
                $game1->setJ1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGame2(): Collection
    {
        return $this->game2;
    }

    public function addGame2(Game $game2): self
    {
        if (!$this->game2->contains($game2)) {
            $this->game2[] = $game2;
            $game2->setJ2($this);
        }

        return $this;
    }

    public function removeGame2(Game $game2): self
    {
        if ($this->game2->contains($game2)) {
            $this->game2->removeElement($game2);
            // set the owning side to null (unless already changed)
            if ($game2->getJ2() === $this) {
                $game2->setJ2(null);
            }
        }

        return $this;
    }

    public function getNotification(): ?int
    {
        return $this->notification;
    }

    public function setNotification(int $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChat(): Collection
    {
        return $this->chat;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chat->contains($chat)) {
            $this->chat[] = $chat;
            $chat->setDestinataire($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chat->contains($chat)) {
            $this->chat->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getDestinataire() === $this) {
                $chat->setDestinataire(null);
            }
        }

        return $this;
    }
}
