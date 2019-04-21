<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ChatGameRepository")
 */
class ChatGame
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $joueur;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="chat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJoueur(): ?int
    {
        return $this->joueur;
    }

    public function setJoueur(int $joueur): self
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPartie(): ?Game
    {
        return $this->partie;
    }

    public function setPartie(?Game $partie): self
    {
        $this->partie = $partie;

        return $this;
    }
}
