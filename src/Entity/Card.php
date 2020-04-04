<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
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
    private $suit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rank;

    /**
     * @ORM\ManyToMany(targetEntity="Player", inversedBy="cards")
     */
    private $client;

    public function __construct()
    {
        $this->client = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuit(): ?string
    {
        return $this->suit;
    }

    public function setSuit(string $suit): self
    {
        $this->suit = $suit;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(Player $client): self
    {
        if (!$this->client->contains($client)) {
            $this->client[] = $client;
        }

        return $this;
    }

    public function removeClient(Player $client): self
    {
        if ($this->client->contains($client)) {
            $this->client->removeElement($client);
        }

        return $this;
    }

    public function toArray(){
        return [
            'rank' => $this->rank,
            'suit' => $this->suit,
        ];
    }
}
