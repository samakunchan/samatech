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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="50")
     */
    private $completeUrl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var UploadedFile $file
     */
    private $file;

    private $tempFileName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $folder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\About", inversedBy="documents")
     */
    private $about;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="icone")
     */
    private $serviceIcone;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="image")
     */
    private $serviceImage;

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

    public function getServiceIcone(): ?Service
    {
        return $this->serviceIcone;
    }

    public function setServiceIcone(?Service $serviceIcone): self
    {
        $this->serviceIcone = $serviceIcone;

        return $this;
    }

    public function getServiceImage(): ?Service
    {
        return $this->serviceImage;
    }

    public function setServiceImage(?Service $serviceImage): self
    {
        $this->serviceImage = $serviceImage;

        return $this;
    }
}
