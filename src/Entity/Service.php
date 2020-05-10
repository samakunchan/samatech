<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
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
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="serviceIcone", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $icone;

    /**
     * TODO Changer ceci en OTO
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="serviceImage", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="services", cascade={"persist"})
     */
    private $user;

    public function __construct()
    {
        $this->icone = new ArrayCollection();
        $this->image = new ArrayCollection();
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

    /**
     * @return Collection|Document[]
     */
    public function getIcone(): Collection
    {
        return $this->icone;
    }

    public function addIcone(Document $icone): self
    {
        if (!$this->icone->contains($icone)) {
            $this->icone[] = $icone;
            $icone->setServiceIcone($this);
        }

        return $this;
    }

    public function removeIcone(Document $icone): self
    {
        if ($this->icone->contains($icone)) {
            $this->icone->removeElement($icone);
            // set the owning side to null (unless already changed)
            if ($icone->getServiceIcone() === $this) {
                $icone->setServiceIcone(null);
            }
        }

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
            $image->setServiceImage($this);
        }

        return $this;
    }

    public function removeImage(Document $image): self
    {
        if ($this->image->contains($image)) {
            $this->image->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getServiceImage() === $this) {
                $image->setServiceImage(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
