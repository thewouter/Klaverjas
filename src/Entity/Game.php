<?php

namespace App\Entity;

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
     * @ORM\Column(type="array")
     */
    private $tricks = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    public function __construct() {
        $this->tricks = [0, 0];
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

    public function getTricks(): ?array
    {
        return $this->tricks;
    }

    public function setTricks(array $tricks): self
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
            $room->setGame($newGame);
        }
        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'room' => $this->getRoom(),
            'points' => $this->getPoints(),
            'tricks' => $this->getTricks(),
        ];
    }
}
