<?php

declare(strict_types=1);

namespace App\State\Call;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MediaObject;
use App\Services\File\UploadedBase64File;

class PostProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $images = $data->getImages();

        /** @var MediaObject $image */
        foreach ($images as $image){
            if ($image->base64content){
                $imageFile = new UploadedBase64File($image->base64content, "call_image.png");
                $image->file = $imageFile;
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
