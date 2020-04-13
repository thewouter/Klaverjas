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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $trump;

    /**
     * @ORM\Column(type="array")
     */
    private $trump_chosen = [null, null, null, null];

    public function __construct() {
        $this->tricks = new ArrayCollection();
    }

    public function getTrump(): ?string
    {
        return $this->trump;
    }

    public function setTrump(?string $trump): self
    {
        $this->trump = $trump;

        return $this;
    }

    public function getTrumpChosen(): array
    {
        return $this->trump_chosen;
    }

    public function setTrumpChosen(array $trump_chosen): self
    {
        $this->trump_chosen = $trump_chosen;

        return $this;
    }

    public function setTrumpChosenSeat(bool $trump_chosen, int $seat): self
    {
        $this->trump_chosen[$seat] = $trump_chosen;

        return $this;
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

    public function addPoints(array $points): self
    {
        $this->points[] = $points;

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

    public function getClassName() {
        return 'game';
    }

    public function resetTrump() {
        $suits = ['c', 'd', 'h', 's'];
        $this->setTrump($suits[array_rand($suits)]);
        $this->setTrumpChosen([null, null, null, null]);
    }

    public function getFirstPlayer() {
        if ($this->getTricks()->count() > 0 ){
            $current_trick = $this->getTricks()->get($this->getTricks()->count() - 1 );
            $first_player = $current_trick->getPlayer1();
            switch ($first_player->getId()) {
                case $this->getRoom()->getUs1()->getId():
                    return 0;
                case $this->getRoom()->getThem1()->getId():
                    return 1;
                case $this->getRoom()->getUs2()->getId():
                    return 2;
                case $this->getRoom()->getThem2()->getId():
                    return 3;
            }
        }

        return 0;
    }

    public function getPrevFirstPlayer() {
        if ($this->getTricks()->count() > 2 ){
            $current_trick = $this->getTricks()->get($this->getTricks()->count() - 2 );
            $first_player = $current_trick->getPlayer1();
            switch ($first_player->getId()) {
                case $this->getRoom()->getUs1()->getId():
                    return 0;
                case $this->getRoom()->getThem1()->getId():
                    return 1;
                case $this->getRoom()->getUs2()->getId():
                    return 2;
                case $this->getRoom()->getThem2()->getId():
                    return 3;
            }
        }

        return 0;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'room' => $this->getRoom()->toArray(),
            'points' => $this->getPoints(),
            'tricks' => array_map(function ($trick) {
                return $trick->toArray();
            }, $this->getTricks()->toArray()),
            'trump' => is_null($this->getTrump()) ? false : $this->getTrump(),
            'trump_chosen' => $this->getTrumpChosen(),
            'first_player' => $this->getFirstPlayer(),
            'prev_first_player' => $this->getPrevFirstPlayer(),
        ];
    }
}
