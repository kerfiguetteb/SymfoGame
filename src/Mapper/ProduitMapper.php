<?php

namespace App\Mapper;

use App\DTO\ProduitDto;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\UrlHelper;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProduitMapper{
    public function __construct(
        private readonly UploaderHelper $uploaderHelper,
        private readonly UrlHelper $urlHelper,
        
        ) {
    }

    public function toDto(Produit $produit): ProduitDto
    {
        $imageUrls = [];
        foreach ($produit->getImages() as $image) {
            if ($image) {
                # code...
                $path = $this->uploaderHelper->asset($image, 'imageFile');
            }
        if ($path) {
           $imageUrls[] = $this->urlHelper->getAbsoluteUrl($path);
        }
        }
        return new ProduitDto(
            id : $produit->getId(),
            name: $produit->getName(),
            description: $produit->getDescription(),
            isActive: $produit->isActive(),
            stock: $produit->isStock(),
            images: $imageUrls
        );
    }
}