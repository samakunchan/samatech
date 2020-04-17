<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{

    const IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
            'save' => 'imagejpeg',
            'quality' => 100
        ],
        IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
            'save' => 'imagepng',
            'quality' => 0
        ],
        IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
        ]
    ];

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
        if (isset($oldFile)) {
            if (file_exists($folder.'/'.$oldFile)) {
                unlink($folder.'/'.$oldFile);
            }
        }
        $this->file->move($folder, $fileName);
    }
    public function clearThumbnail($folder, $filename, $oldFile)
    {
        if(null === $filename){
            return;
        }
        if (isset($oldFile)) {
            if (file_exists($folder.'/'.$oldFile)) {
                unlink($folder.'/'.$oldFile);
            }
        }
    }

    public function createThumbnail($src, $dest, $targetWidth, $targetHeight = null)
    {

        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource

        // get the type of the image
        // we need the type to determine the correct loader
        $type = exif_imagetype($src);

        // if no valid type or no handler found -> exit
        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return null;
        }

        // load the image with the correct loader
        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src);

        // no image found at supplied location -> exit
        if (!$image) {
            return null;
        }


        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail

        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);

        // maintain aspect ratio when no height set
        if ($targetHeight == null) {

            // get width to height ratio
            $ratio = $width / $height;

            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            }
            // if is landscape
            // use ratio to scale width to fit in square
            else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

            // make image transparent
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0) );

            // additional settings for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // copy entire source image to duplicate image and resize
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);


        // 3. Save the $thumbnail to disk
        // - call the correct save method
        // - set the correct quality level

        // save the duplicate version of the image to disk
        return call_user_func(self::IMAGE_HANDLERS[$type]['save'], $thumbnail, $dest, self::IMAGE_HANDLERS[$type]['quality']);
    }

}
