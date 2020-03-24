<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="array", nullable=true)
     */
    private $cards = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Room", mappedBy="us1", cascade={"persist", "remove"})
     */
    private $room;

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

    public function getCards(): ?array
    {
        return $this->cards;
    }

    public function setCards(?array $cards): self
    {
        $this->cards = $cards;

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
        $newUs1 = null === $room ? null : $this;
        if ($room->getUs1() !== $newUs1) {
            $room->setUs1($newUs1);
        }

        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'room' => $this->getRoom(),
            'cards' => $this->getCards(),
        ];
    }
}
