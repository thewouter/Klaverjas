<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function Clue\StreamFilter\fun;

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
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_1;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_2;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player_3;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
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

    public static $NORMAL_ORDER = [0 => '7', 1 => '8', 2 => '9', 3 => 'j', 4 => 'q', 5 => 'k', 6 => 't', 7 => 'a'];
    public static $TRUMP_ORDER = [0 => '7', 1 => '8', 2 => 'q', 3 => 'k', 4 => 't', 5 => 'a', 6 => '9', 7 => 'j'];
    public static $MELD_ORDER = [0 => '7', 1 => '8', 2 => '9', 3 => 't', 4 => 'j', 5 => 'q', 6 => 'k', 7 => 'a'];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer1(): ?Player
    {
        return $this->player_1;
    }

    public function setPlayer1(?Player $player_1): self
    {
        $this->player_1 = $player_1;

        return $this;
    }

    public function getPlayer2(): ?Player
    {
        return $this->player_2;
    }

    public function setPlayer2(?Player $player_2): self
    {
        $this->player_2 = $player_2;

        return $this;
    }

    public function getPlayer3(): ?Player
    {
        return $this->player_3;
    }

    public function setPlayer3(?Player $player_3): self
    {
        $this->player_3 = $player_3;

        return $this;
    }

    public function getPlayer4(): ?Player
    {
        return $this->player_4;
    }

    public function setPlayer4(?Player $player_4): self
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
            return false;
        }
    }

    public function setNextCard(Card $card, Player $client){
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

    public function getClassName() {
        return 'trick';
    }

    public function getNextPlayer() {
        if (!in_array(true, $this->getGame()->getTrumpChosen())) {
            $chosenTrump = $this->getGame()->getTrumpChosen();
            switch (true) {
                case (is_null($chosenTrump[0])):
                    return 0;
                    break;
                case (is_null($chosenTrump[1])):
                    return 1;
                    break;
                case (is_null($chosenTrump[2])):
                    return 2;
                    break;
                case (is_null($chosenTrump[3])):
                    return 3;
                    break;
                default: // Everybody passed, player 1 has to choose
                    return 0;
            }
        }

        switch (true) {
            case is_null($this->getCard1()):
                return 0;
                break;
            case is_null($this->getCard2()):
                return 1;
                break;
            case is_null($this->getCard3()):
                return 2;
                break;
            case is_null($this->getCard4()):
                return 3;
                break;
        }
    }

    public function getWinnerTrump($trumper){
        $winner = $trumper;
        $trump = $this->getGame()->getTrump();
        foreach (range(0, 7) as $key) {
            $value = self::$TRUMP_ORDER[$key];
            if(!is_null($this->getCard1()) && $this->getCard1()->getRank() == $value && $this->getCard1()->getSuit() == $trump){
                $winner = 0;
            }
            if(!is_null($this->getCard2()) && $this->getCard2()->getRank() == $value && $this->getCard2()->getSuit() == $trump){
                $winner = 1;
            }
            if(!is_null($this->getCard3()) && $this->getCard3()->getRank() == $value && $this->getCard3()->getSuit() == $trump){
                $winner = 2;
            }
            if(!is_null($this->getCard4()) && $this->getCard4()->getRank() == $value && $this->getCard4()->getSuit() == $trump){
                $winner = 3;
            }
        }
        return $winner;
    }

    public function getWinner() {
        if(is_null($this->getCard1())) {
            return null;
        }
        if(!is_null($this->getCard1()) && $this->getGame()->getTrump() == $this->getCard1()->getSuit()){
            return $this->getWinnerTrump(0);
        } else {
            if(!is_null($this->getCard2()) && $this->getCard2()->getSuit() == $this->getGame()->getTrump()){
                return $this->getWinnerTrump(1);
            } elseif (!is_null($this->getCard3()) && $this->getCard3()->getSuit() == $this->getGame()->getTrump()) {
                return $this->getWinnerTrump(2);
            } elseif (!is_null($this->getCard4()) && $this->getCard4()->getSuit() == $this->getGame()->getTrump()){
                return $this->getWinnerTrump(3);
            }
            $winner = 0;
            $playedSuit = $this->getCard1()->getSuit();
            foreach (range(0, 7) as $key) {
                $value = self::$NORMAL_ORDER[$key];
                if(!is_null($this->getCard1()) && $this->getCard1()->getRank() == $value && $this->getCard1()->getSuit() == $playedSuit){
                    $winner = 0;
                }
                if(!is_null($this->getCard2()) && $this->getCard2()->getRank() == $value && $this->getCard2()->getSuit() == $playedSuit){
                    $winner = 1;
                }
                if(!is_null($this->getCard3()) && $this->getCard3()->getRank() == $value && $this->getCard3()->getSuit() == $playedSuit){
                    $winner = 2;
                }
                if(!is_null($this->getCard4()) && $this->getCard4()->getRank() == $value && $this->getCard4()->getSuit() == $playedSuit){
                    $winner = 3;
                }
            }
            return $winner;
        }
    }

    public function getMeld() {
        if(is_null($this->getCard4())) {
            return 0;
        }
        $cards = [
            $this->getCard1(),
            $this->getCard2(),
            $this->getCard3(),
            $this->getCard4(),
        ];
        $meld = 0;
        $suits = ['d', 's', 'h', 'c'];
        foreach ($suits as $suit) {
            $per_suit = array_filter($cards, function ($c) use ($suit) {
                return $c->getSuit() == $suit;
            });

            $keys = array_map(function ($c) {
                return array_search($c->getRank(), self::$MELD_ORDER);
            }, $per_suit);

            sort($keys);

            if (count($keys) == 4){
                if($keys[1] == ($keys[0] + 1) && $keys[2] == ($keys[1] + 1) && $keys[3] == ($keys[2] + 1)) {
                    $meld += 50;
                } elseif ($keys[1] == ($keys[0] + 1) && $keys[2] == ($keys[1] + 1)) {
                    $meld += 20;
                } elseif ($keys[2] == ($keys[1] + 1) && $keys[3] == ($keys[2] + 1)) {
                    $meld += 20;
                }
            } elseif (count($keys) == 3) {
                if ($keys[1] == ($keys[0] + 1) && $keys[2] == ($keys[1] + 1)) {
                    $meld += 20;
                }
            }

            if ($suit == $this->getGame()->getTrump()){
                $ranks = array_map(function ($c) {
                    return $c->getRank();
                }, $per_suit);
                if (in_array('k', $ranks) && in_array('q', $ranks)) {
                    $meld += 20;
                }
            }
        }

        $ranks = array_map(function ($card) {
            return $card->getRank();
        }, $cards);

        if (count($ranks) == 4) {
            if($ranks[0] == $ranks[1] && $ranks[1] == $ranks[2] && $ranks[2] == $ranks[3]) {
                if ($ranks[0] == 'j') {
                    $meld += 200;
                } else {
                    $meld += 100;
                }
            }
        }

        return $meld;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'game' => $this->getGame()->getId(),
            'player_1' => $this->getPlayer1()->toArray(),
            'player_2' => $this->getPlayer2()->toArray(),
            'player_3' => $this->getPlayer3()->toArray(),
            'player_4' => $this->getPlayer4()->toArray(),
            'card_1' => is_null($this->getCard1()) ? false : $this->getCard1()->toArray(),
            'card_2' => is_null($this->getCard2()) ? false : $this->getCard2()->toArray(),
            'card_3' => is_null($this->getCard3()) ? false : $this->getCard3()->toArray(),
            'card_4' => is_null($this->getCard4()) ? false : $this->getCard4()->toArray(),
            'turn' => $this->getNextPlayer(),
            'meld' => $this->getMeld(),
            'winner' => $this->getWinner(),
        );
    }
}
