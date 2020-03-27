<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function Clue\StreamFilter\fun;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Card", mappedBy="client", cascade={"persist", "remove"})
     */
    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
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

    private $room;

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        // set (or unset) the owning side of the relation if necessary
        $newUs1 = null === $room ? null : $this;
        if ($room->getUs1() !== $newUs1) {
            $room->setUs1($newUs1);
        }

        return $this;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->addClient($this);
        }

        return $this;
    }

    public function addCards(array $cards): self
    {
        foreach ($cards as $card){
            $this->addCard($card);
        }
        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            $card->removeClient($this);
        }

        return $this;
    }

    public function removeAllCards(): self
    {
        foreach ($this->getCards() as $card) {
            $this->removeCard($card);
        }

        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'room' => $this->getRoom(),
            'cards' => array_map(function ($card) {
                return $card->toArray();
            }, $this->getCards()->toArray()),
        ];
    }
}
