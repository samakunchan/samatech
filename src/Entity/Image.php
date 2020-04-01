<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\About", inversedBy="images", cascade={"persist"})
     */
    private $about;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $folder;

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
     * @return Image
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        if (isset($this->completeUrl)) {
            // store the old name to delete after the update
            $this->tempFileName = $this->completeUrl;
            $this->completeUrl = null;
            // dd(isset($this->completeUrl));
        }
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
        if ($this->file === null){
            return;
        }
        // d($this->completeUrl);
        if ($this->completeUrl !== null) {
            $this->tempFileName = $this->completeUrl;
            // dd($this->tempFileName);
        }
        $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$this->file->guessExtension();
        $this->completeUrl = $fileName;
        // dd($this->completeUrl, $this->tempFileName);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if(null === $this->file){
            return;
        }

        // Si on avait un ancien fichier, on le supprime
        // dd($this->tempFileName);
        if (isset($this->tempFileName)) {
            $oldFile = $this->getUploadRootDir().'/'.$this->tempFileName;
            // dd($oldFile);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->completeUrl   // Le nom du fichier à créer, ici « id.ext »
        );
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFileName = $this->getUploadRootDir().'/'.$this->completeUrl;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFileName)) {
            // On supprime le fichier
            dump($this->tempFileName);
            unlink($this->tempFileName);
        }
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/'.$this->folder;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__ . '/../../public/'.$this->getUploadDir();
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
}
