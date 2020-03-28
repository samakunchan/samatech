<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RemembermeTokenRepository")
 */
class RemembermeToken
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="string", length=88, options={"fixed" = true})
     * @Assert\NotBlank()
     * @Assert\Length(88)
     */
    private $series;

    /**
     * @ORM\Column(type="string", length=88, options={"fixed" = true})
     * @Assert\NotBlank()
     * @Assert\Length(88)
     */
    private $value;

    /**
     * @ORM\Column(name="lastUsed", type="datetime")
     * @Assert\DateTime()
     */
    private $lastUsed;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max="100")
     * @Assert\IdenticalTo("App\Entity\User", message="Le champ doit utiliser la class User")
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank()
     * @Assert\Length(max="200")
     * @Assert\IdenticalTo("sam@test.fr", message="Le champ doit utiliser la class sam@test.fr")
     */
    private $username;


    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLastUsed(): ?DateTimeInterface
    {
        return $this->lastUsed;
    }

    public function setLastUsed(DateTimeInterface $lastUsed): self
    {
        $this->lastUsed = $lastUsed;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
