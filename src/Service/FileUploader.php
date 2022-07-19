<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;



class FileUploader
{
    private $targetDirectory;
    private $slugger;
    private $kernelDirectory;


    public function __construct(
        $targetDirectory,
        SluggerInterface $slugger,
        $kernelDirectory

    )
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->kernelDirectory = $kernelDirectory;

    }

 

    public function upload(UploadedFile $file, string $targetDirectory)
    {

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
        
        try {
            $file->move(
                $this->getTargetDirectory($targetDirectory),
                $newFileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $newFileName;
    }

    public function getTargetDirectory(string $targetDirectory)
    {
        return $this->targetDirectory . $targetDirectory;
    }
    public static function getRelativePath($file)
    {
        dump(FileUploader::$kernelDirectory);
    }

 
    

  




 


}