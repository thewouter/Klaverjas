<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=255, options={"collation":"utf8_bin"})
     * @Assert\Length(
     *      min = 1,
     *      max = 49,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     *
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Player", mappedBy="client", cascade={"persist", "remove"})
     */
    private $player;


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

    public function getClassName() {
        return 'client';
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
        );
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        // set (or unset) the owning side of the relation if necessary
        $newClient = null === $player ? null : $this;
        if ($player->getClient() !== $newClient) {
            $player->setClient($newClient);
        }

        return $this;
    }
}
