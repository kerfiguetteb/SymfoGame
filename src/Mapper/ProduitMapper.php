<?php

namespace App\Mapper;

use App\Entity\Produit;
use App\DTO\ProduitDTO;
use Symfony\Component\HttpFoundation\UrlHelper;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProduitMapper
{
    public function __construct(
        private readonly UploaderHelper $uploaderHelper,
        private readonly UrlHelper $urlHelper,
    ) {}
    public function toDto(Produit $produit): ProduitDTO
    {
        $imageUrls = [];
        foreach ($produit->getImages() as $image) {
            $path = $this->uploaderHelper->asset($image, 'imageFile');

            if ($path) {
                $imageUrls[] = $this->urlHelper->getAbsoluteUrl($path);
            }
        }
        return new ProduitDTO(
            id: $produit->getId(),
            name: $produit->getName(),
            description: $produit->getDescription(),
            isActive: $produit->isActive(),
            stock: $produit->isStock(),
            images: $imageUrls
        );
    }
}
