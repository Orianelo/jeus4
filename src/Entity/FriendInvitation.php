<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendInvitationRepository")
 */
class FriendInvitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getJoueurInvitant()
    {
        return $this->joueurInvitant;
    }

    /**
     * @param mixed $joueurInvitant
     */
    public function setJoueurInvitant($joueurInvitant): void
    {
        $this->joueurInvitant = $joueurInvitant;
    }

    /**
     * @return mixed
     */
    public function getJoueurInvite()
    {
        return $this->joueurInvite;
    }

    /**
     * @param mixed $joueurInvite
     */
    public function setJoueurInvite($joueurInvite): void
    {
        $this->joueurInvite = $joueurInvite;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $joueurInvitant;

    /**
     * @ORM\Column(type="integer")
     */
    private $joueurInvite;

    public function getId(): ?int
    {
        return $this->id;
    }


}
