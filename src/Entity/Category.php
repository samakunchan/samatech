<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Type("string")
     * @Assert\Length(min="3", minMessage="Le type doit avoir au moins {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @Assert\Type("string")
     * @Assert\Length(min="6", minMessage="La description doit avoir au moins {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\Environnement", inversedBy="categories")
     */
    private $environnement;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Portfolio", mappedBy="category", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $portfolios;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Blog", mappedBy="category")
     */
    private $blogs;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Contact", mappedBy="categories")
     */
    private $contacts;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le slug n'a pas pu être effectuer.")
     * @Assert\Length(min="3", minMessage="Le slug doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @Assert\Type("string")
     * @Assert\Length(min="5", minMessage="Le code de l'icone doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icone;

    public function __construct()
    {
        $this->portfolios = new ArrayCollection();
        $this->blogs = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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

    /**
     * @return Collection|Blog[]
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs[] = $blog;
            $blog->setCategory($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        if ($this->blogs->contains($blog)) {
            $this->blogs->removeElement($blog);
            // set the owning side to null (unless already changed)
            if ($blog->getCategory() === $this) {
                $blog->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCategory($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getCategory() === $this) {
                $contact->setCategory(null);
            }
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

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }
}
