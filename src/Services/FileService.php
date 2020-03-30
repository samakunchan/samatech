<?php
# Service FileService complet

namespace App\Service;

use App\Entity\Document;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, Document $document, $type)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $document->setType($type);
        $document->setPath($fileName);
        if (!$document->getCreatedAt()) {
            $document->setCreatedAt(new DateTime('now'));
        }
        $document->setUpdatedAt(new DateTime('now'));
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }


    public function multiUpload($files, $type) // reçoit a un tableau de fichier
    {
        $allUrl = [];
        foreach($files as $file)
        {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            $document = new Document();
            $document->setType($type);
            $document->setPath($fileName);

            try {
                $file->move($this->getTargetDirectory(), $fileName);
                $allUrl[] = $document;
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }


        return $allUrl;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}
