<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AboutRepository")
 */
class About
{
    public const DEFAULT_SLUG = 'a propos de moi';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(min="3", minMessage="Le titre doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(min="6", minMessage="La description doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\Type("datetime")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le slug n'a pas pu être effectuer.")
     * @Assert\Length(min="3", minMessage="Le slug doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=15)
     */
    private $slug;

    /**
     * @Assert\Type("object")
     * @ORM\OneToOne(targetEntity="App\Entity\Document", cascade={"persist", "remove"})
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $cleanText = preg_replace('/\W+/', '-', $slug);
        $cleanText = strtolower(trim($cleanText, '-'));
        $this->slug = $cleanText;

        return $this;
    }

    public function getImage(): ?Document
    {
        return $this->image;
    }

    public function setImage(?Document $image): self
    {
        $this->image = $image;

        return $this;
    }
}
