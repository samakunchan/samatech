<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    /**
     * @var string
     */
    private $folder;
    /**
     * @var UploadedFile $file
     */
    private $file;

    public function transformToUrl(UploadedFile $file)
    {
        $this->file = $file;
        $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$this->file->guessExtension();
        if ($this->file->getMimeType() === 'application/pdf') {
            $this->folder = 'pdf';
        } else if ($this->file->getMimeType() === 'image/png' || $this->file->getMimeType() === 'image/jpg' || $this->file->getMimeType() === 'image/jpeg') {
            $this->folder = 'images';
        } else {
            $this->folder = 'non_repertorier';
        }
        return ['filename' => $fileName, 'folder' => $this->folder];
    }

    public function moveToFolder($folder, $fileName)
    {
        $this->file->move($folder, $fileName);
    }

    public function uploadFolder($folder, $fileName, $oldFile)
    {
        if(null === $fileName){
            return;
        }
        // dump($folder, $fileName, $oldFile);
        // dd(isset($fileName));
        if (isset($fileName)) {
            if (file_exists($folder.'/'.$oldFile)) {
                unlink($folder.'/'.$oldFile);
            }
        }
        $this->file->move($folder, $fileName);
    }
}
