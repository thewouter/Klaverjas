<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_3;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_4;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     */
    private $card_1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     */
    private $card_2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     */
    private $card_3;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     */
    private $card_4;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer1(): ?Client
    {
        return $this->player_1;
    }

    public function setPlayer1(?Client $player_1): self
    {
        $this->player_1 = $player_1;

        return $this;
    }

    public function getPlayer2(): ?Client
    {
        return $this->player_2;
    }

    public function setPlayer2(?Client $player_2): self
    {
        $this->player_2 = $player_2;

        return $this;
    }

    public function getPlayer3(): ?Client
    {
        return $this->player_3;
    }

    public function setPlayer3(?Client $player_3): self
    {
        $this->player_3 = $player_3;

        return $this;
    }

    public function getPlayer4(): ?Client
    {
        return $this->player_4;
    }

    public function setPlayer4(?Client $player_4): self
    {
        $this->player_4 = $player_4;

        return $this;
    }

    public function getCard1(): ?Card
    {
        return $this->card_1;
    }

    public function setCard1(?Card $card_1): self
    {
        $this->card_1 = $card_1;

        return $this;
    }

    public function getCard2(): ?Card
    {
        return $this->card_2;
    }

    public function setCard2(?Card $card_2): self
    {
        $this->card_2 = $card_2;

        return $this;
    }

    public function getCard3(): ?Card
    {
        return $this->card_3;
    }

    public function setCard3(?Card $card_3): self
    {
        $this->card_3 = $card_3;

        return $this;
    }

    public function getCard4(): ?Card
    {
        return $this->card_4;
    }

    public function setCard4(?Card $card_4): self
    {
        $this->card_4 = $card_4;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getCurrentPlayer(){
        if(is_null($this->card_1)) {
            return $this->player_1;
        } elseif(is_null($this->card_2)) {
            return $this->player_2;
        } elseif(is_null($this->card_3)) {
            return $this->player_3;
        } elseif(is_null($this->card_4)){
            return $this->player_4;
        } else {
            return null;
        }
    }

    public function setNextCard(Card $card, Client $client){
        if(is_null($this->card_1)) {
            $this->card_1 = $card;
            $this->player_1 = $client;
        } elseif(is_null($this->card_2)) {
            $this->card_2 = $card;
            $this->player_2 = $client;
        } elseif(is_null($this->card_3)) {
            $this->card_3 = $card;
            $this->player_3 = $client;
        } elseif(is_null($this->card_4)){
            $this->card_4 = $card;
            $this->player_4 = $client;
        } else {
            return false;
        }
        return true;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'player_1' => $this->getPlayer1()->toArray(),
            'player_2' => $this->getPlayer2()->toArray(),
            'player_3' => $this->getPlayer3()->toArray(),
            'player_4' => $this->getPlayer4()->toArray(),
            'card_1' => is_null($this->getCard1()) ? false : $this->getCard1()->toArray(),
            'card_2' => is_null($this->getCard2()) ? false : $this->getCard2()->toArray(),
            'card_3' => is_null($this->getCard3()) ? false : $this->getCard3()->toArray(),
            'card_4' => is_null($this->getCard4()) ? false : $this->getCard4()->toArray(),
        ];
    }
}
