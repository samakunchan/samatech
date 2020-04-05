<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    /**
     * @var string
     */
    private $folder;

    public function transformToUrl(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        if ($file->getMimeType() === 'application/pdf') {
            $this->folder = 'pdf';
        } else if ($file->getMimeType() === 'image/png' || $file->getMimeType() === 'image/jpg' || $file->getMimeType() === 'image/jpeg') {
            $this->folder = 'images';
        } else {
            $this->folder = 'non_repertorier';
        }
        return ['filename' => $fileName, 'folder' => $this->folder];
    }

    public function moveToFolder(UploadedFile $file, $folder, $fileName)
    {
        $file->move($folder, $fileName);
    }
}
