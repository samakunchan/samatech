<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", minMessage="Votre message doit avoir au moins {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     */
    private $name;

    /**
     * @Assert\Type("string")
     * @Assert\Email(message="La valeur indiqué n'est pas un email valide.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="contacts")
     */
    private $categories;

    /**
     * @Assert\Type("string")
     * @Assert\Length(min="6", minMessage="Votre message doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="contact", cascade={"persist","remove"})
     */
    private $document;

    /**
     * @Assert\Type("datetime")
     * @ORM\Column(type="datetime")
     */
    private $contactedAt;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide")
     * @Assert\Length(min="10", minMessage="Votre message doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @Assert\Type("bool")
     * @ORM\Column(type="boolean")
     */
    private $readed;

    public function __construct()
    {
        $this->document = new ArrayCollection();
        $this->readed = false;
    }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->categories;
    }

    public function setCategory(?Category $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocument(): Collection
    {
        return $this->document;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->document->contains($document)) {
            $this->document[] = $document;
            $document->setContact($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->document->contains($document)) {
            $this->document->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getContact() === $this) {
                $document->setContact(null);
            }
        }

        return $this;
    }

    public function getContactedAt(): ?DateTimeInterface
    {
        return $this->contactedAt;
    }

    public function setContactedAt(DateTimeInterface $contactedAt): self
    {
        $this->contactedAt = $contactedAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getReaded(): ?bool
    {
        return $this->readed;
    }

    public function setReaded(bool $readed): self
    {
        $this->readed = $readed;

        return $this;
    }
}
