<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert|NotBlank
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="room", cascade={"persist", "remove"})
     */
    private $us1;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="room", cascade={"persist", "remove"})
     */
    private $us2;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="room", cascade={"persist", "remove"})
     */
    private $them1;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="room", cascade={"persist", "remove"})
     */
    private $them2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="room", orphanRemoval=true, cascade={"persist"})
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUs1(): ?Client
    {
        return $this->us1;
    }

    public function setUs1(?Client $us1): self
    {
        $this->us1 = $us1;

        return $this;
    }

    public function getUs2(): ?Client
    {
        return $this->us2;
    }

    public function setUs2(?Client $us2): self
    {
        $this->us2 = $us2;

        return $this;
    }

    public function getThem1(): ?Client
    {
        return $this->them1;
    }

    public function setThem1(?Client $them1): self
    {
        $this->them1 = $them1;

        return $this;
    }

    public function getThem2(): ?Client
    {
        return $this->them2;
    }

    public function setThem2(?Client $them2): self
    {
        $this->them2 = $them2;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function hasGame(Game $game) {
        return in_array($game, $this->games->toArray());
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setRoom($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getRoom() === $this) {
                $game->setRoom(null);
            }
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'us1' => $this->getUs1()->getId(),
            'us2' => $this->getUs2()->getId(),
            'them1' => $this->getThem1()->getId(),
            'them2' => $this->getThem2()->getId(),
            'games' => array_map(function ($game) {
                return $game->getId();
                }, $this->getGames()),
        ];
    }

}
