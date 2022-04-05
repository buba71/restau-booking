<?php

declare(strict_types=1);

namespace App\Services\UploadFilesServices;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ImageUpLoaderHelper
{
    public function __construct(private ParameterBagInterface $parameters, private SluggerInterface $slugger) {}

    public function uploadRestaurantImage(UploadedFile $uploadedFile): string
    {        
        $destination  = $this->parameters->get('kernel.project_dir') . '/public/uploads/images';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $this->slugger->slug($originalFilename)->toString() .'-'.uniqid().'.'.$uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFileName
        );

        return $newFileName;
    }
}
