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
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Player", cascade={"persist", "remove"})
     */
    private $us1;

    /**
     * @ORM\OneToOne(targetEntity="Player", cascade={"persist", "remove"})
     */
    private $us2;

    /**
     * @ORM\OneToOne(targetEntity="Player", cascade={"persist", "remove"})
     */
    private $them1;

    /**
     * @ORM\OneToOne(targetEntity="Player", cascade={"persist", "remove"})
     */
    private $them2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="room", orphanRemoval=true, cascade={"persist"})
     */
    private $games;

    /**
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $in_game = false;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->us1 = new Player();
        $this->us2 = new Player();
        $this->them1 = new Player();
        $this->them2 = new Player();
    }

    public function removeClient(Player $client){
        if ($this->us1 == $client){
            $this->us1 = null;
        }
        if ($this->us2 == $client){
            $this->us2 = null;
        }
        if ($this->them1 == $client){
            $this->them1 = null;
        }
        if ($this->them2 == $client){
            $this->them2 = null;
        }
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

    public function getUs1(): ?Player
    {
        return $this->us1;
    }

    public function setUs1(?Player $us1): self
    {
        $this->us1 = $us1;

        return $this;
    }

    public function getUs2(): ?Player
    {
        return $this->us2;
    }

    public function setUs2(?Player $us2): self
    {
        $this->us2 = $us2;

        return $this;
    }

    public function getThem1(): ?Player
    {
        return $this->them1;
    }

    public function setThem1(?Player $them1): self
    {
        $this->them1 = $them1;

        return $this;
    }

    public function getThem2(): ?Player
    {
        return $this->them2;
    }

    public function setThem2(?Player $them2): self
    {
        $this->them2 = $them2;

        return $this;
    }

    public function getGame(): ?Game{
        if ($this->getInGame()){
            return $this->games->last();
        }
        return null;

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

    public function getInGame(): ?bool
    {
        return $this->in_game;
    }

    public function setInGame(bool $in_game): self
    {
        $this->in_game = $in_game;

        return $this;
    }

    public function isFull(){
        return !is_null($this->us1) && !is_null($this->us2) && !is_null($this->them1) && !is_null($this->them2);
    }

    public function hasPlayer(Player $client) {
        if ($this->us1 === $client){
            return true;
        }
        if ($this->us2 === $client){
            return true;
        }
        if ($this->them1 === $client){
            return true;
        }
        if ($this->them2 === $client){
            return true;
        }
        return false;
    }

    public function getClassName() {
        return 'room';
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'us1' => $this->getUs1()->toArray(),
            'us2' => $this->getUs2()->toArray(),
            'them1' => $this->getThem1()->toArray(),
            'them2' => $this->getThem2()->toArray(),
            'in_game' => $this->getInGame(),
            'games' => array_map(function ($game) {
                return $game->getId();
            }, $this->getGames()->toArray()),
            'current_game' => is_null($this->getGame()) ? false : $this->getGame()->getId(),
        ];
    }
}
