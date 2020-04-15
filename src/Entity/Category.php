<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="category", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Environnement", inversedBy="categories")
     */
    private $environnement;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Portfolio", mappedBy="category", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $portfolios;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->portfolios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Document $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setCategory($this);
        }

        return $this;
    }

    public function removeImage(Document $image): self
    {
        if ($this->image->contains($image)) {
            $this->image->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getCategory() === $this) {
                $image->setCategory(null);
            }
        }

        return $this;
    }

    public function getEnvironnement(): ?Environnement
    {
        return $this->environnement;
    }

    public function setEnvironnement(?Environnement $environnement): self
    {
        $this->environnement = $environnement;

        return $this;
    }

    /**
     * @return Collection|Portfolio[]
     */
    public function getPortfolios(): Collection
    {
        return $this->portfolios;
    }

    public function addPortfolio(Portfolio $portfolio): self
    {
        if (!$this->portfolios->contains($portfolio)) {
            $this->portfolios[] = $portfolio;
            $portfolio->setCategory($this);
        }

        return $this;
    }

    public function removePortfolio(Portfolio $portfolio): self
    {
        if ($this->portfolios->contains($portfolio)) {
            $this->portfolios->removeElement($portfolio);
            // set the owning side to null (unless already changed)
            if ($portfolio->getCategory() === $this) {
                $portfolio->setCategory(null);
            }
        }

        return $this;
    }
}
