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
     * @ORM\Column(type="array")
     */
    private $points = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="game", orphanRemoval=true)
     */
    private $tricks;

    public function __construct() {
        $this->tricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?array
    {
        return $this->points;
    }

    public function setPoints(array $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getTricks()
    {
        return $this->tricks;
    }

    public function setTricks(ArrayCollection $tricks): self
    {
        $this->tricks = $tricks;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        // set (or unset) the owning side of the relation if necessary
        $newGame = null === $room ? null : $this;
        if (!$room->hasGame($this)) {
            $room->addGame($newGame);
        }
        return $this;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setGame($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getGame() === $this) {
                $trick->setGame(null);
            }
        }

        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'room' => $this->getRoom()->toArray(),
            'points' => $this->getPoints(),
            'tricks' => array_map(function ($trick) {
                return $trick->toArray();
            }, $this->getTricks()->toArray()),
        ];
    }
}
