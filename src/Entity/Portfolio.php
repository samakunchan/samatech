<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PortfolioRepository")
 */
class Portfolio
{
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
     * @Assert\Type("object")
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="portfolios")
     */
    private $category;

    /**
     * @Assert\Valid
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="portfolios", cascade={"persist", "remove"})
     */
    private $tags;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le slug n'a pas pu être effectuer.")
     * @Assert\Length(min="3", minMessage="Le slug doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @Assert\Type("object")
     * @ORM\OneToOne(targetEntity="App\Entity\Document", cascade={"persist", "remove"})
     */
    private $image;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

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
