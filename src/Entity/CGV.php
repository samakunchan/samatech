<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CGVRepository")
 */
class CGV
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @Assert\IdenticalTo("Conditions générales de vente", message="Le champ doit être identique à {{ compared_value_type }} {{ compared_value }}")
     * @Assert\Length(min="6", minMessage="Le titre doit avoir au moins {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(min="6", minMessage="La contenu doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le slug n'a pas pu être effectuer.")
     * @Assert\Length(min="3", minMessage="Le slug doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
}
