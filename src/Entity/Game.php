<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     */
    private $terrain_j1;

    /**
     * @ORM\Column(type="json_array")
     */
    private $terrain_j2;

    /**
     * @ORM\Column(type="integer")
     */
    private $tour;

    /**
     * @ORM\Column(type="json_array")
     */
    private $de;

    /**
     * @ORM\Column(type="integer")
     */
    private $tour_joueur;

    /**
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_en_cours;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_fin;

    /**
     * Victoire ninja 1
     * Victoire maison 2
     * victoire abandon 3
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    private $gagnant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type_victoire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="game1")
     * @ORM\JoinColumn(nullable=true)
     */
    private $j1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="game2")
     * @ORM\JoinColumn(nullable=false)
     */
    private $j2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Chat", mappedBy="partie")
     */
    private $chat;

    public function __construct()
    {
        $this->tour=1;
        $this->etat = 1;
        $this->date_debut = new \DateTime();
        $this->date_en_cours = new \DateTime();
        $this->tour_joueur = 1;
        $this->de='';
        $this->chat = new ArrayCollection();

    }

    public function getJ1(): ?Player
    {
        return $this->j1;
    }

    public function setJ1(?Player $j1): self
    {
        $this->j1 = $j1;

        return $this;
    }

    public function getJ2(): ?Player
    {
        return $this->j2;
    }

    public function setJ2(?Player $j2): self
    {
        $this->j2 = $j2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTerrainJ1()
    {
        return $this->terrain_j1;
    }

    /**
     * @param mixed $terrain_j1
     */
    public function setTerrainJ1($terrain_j1): void
    {
        $this->terrain_j1 = $terrain_j1;
    }

    /**
     * @return mixed
     */
    public function getTerrainJ2()
    {
        return $this->terrain_j2;
    }

    /**
     * @param mixed $terrain_j2
     */
    public function setTerrainJ2($terrain_j2): void
    {
        $this->terrain_j2 = $terrain_j2;
    }

    public function getTour(): ?int
    {
        return $this->tour;
    }

    public function setTour(int $tour): self
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDe()
    {
        return $this->de;
    }

    /**
     * @param mixed $de
     */
    public function setDe($de): void
    {
        $this->de = $de;
    }

    public function getTourJoueur(): ?int
    {
        return $this->tour_joueur;
    }

    public function setTourJoueur(int $tour_joueur): self
    {
        $this->tour_joueur = $tour_joueur;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateEnCours(): ?\DateTimeInterface
    {
        return $this->date_en_cours;
    }

    public function setDateEnCours(?\DateTimeInterface $date_en_cours): self
    {
        $this->date_en_cours = $date_en_cours;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getGagnant(): ?int
    {
        return $this->gagnant;
    }

    public function setGagnant(?int $gagnant): self
    {
        $this->gagnant = $gagnant;

        return $this;
    }

    public function getTypeVictoire(): ?int
    {
        return $this->type_victoire;
    }

    public function setTypeVictoire(?int $type_victoire): self
    {
        $this->type_victoire = $type_victoire;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
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
            $chat->setPartie($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chat->contains($chat)) {
            $this->chat->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getPartie() === $this) {
                $chat->setPartie(null);
            }
        }

        return $this;
    }
}
