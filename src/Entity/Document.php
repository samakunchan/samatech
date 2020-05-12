<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @Assert\Length(min="3", minMessage="Le titre doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="50")
     */
    private $completeUrl;

    /**
     * @Assert\Type("datetime")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Choice({"images", "pdf", "non-repertorier"}, message="Veuillez choisir parmis les valeurs autorisés: {{ choices }}")
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $folder;

    /**
     * @Assert\Type("string")
     * @Assert\Length(min="3", minMessage="Le titre doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\About", inversedBy="documents")
     */
    private $about;

    /**
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="document")
     */
    private $contact;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(min="3", minMessage="Le titre doit avoir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string", length=255)
     */
    private $ext;

    /**
     * @Assert\File(
     *     maxSize = "1800k",
     *     maxSizeMessage="La taille du fichier est élévé: {{ size }} {{ suffix }}. Maximum: {{ limit }} {{ suffix }}",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg, application/pdf", "application/x-pdf", "text/plain"},
     *     mimeTypesMessage = "Seul les fichiers de type {{ types }} sont autorisés.",
     * )
     * @var UploadedFile $file
     */
    private $file;

    private $tempFileName;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     * @return Document
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        if (isset($this->completeUrl)) {
            $this->tempFileName = $this->completeUrl;
            $this->completeUrl = null;
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompleteUrl(): ?string
    {
        return $this->completeUrl;
    }

    public function setCompleteUrl(string $completeUrl): self
    {
        $this->completeUrl = $completeUrl;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAbout(): ?About
    {
        return $this->about;
    }

    public function setAbout(?About $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(?string $folder): self
    {
        $this->folder = $folder;

        return $this;
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

    public function getTempFileName()
    {
        return $this->tempFileName;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(string $ext): self
    {
        $this->ext = $ext;

        return $this;
    }
}
