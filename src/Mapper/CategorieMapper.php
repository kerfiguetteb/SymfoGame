<?php

namespace App\Mapper;

use App\DTO\CategorieDto;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\UrlHelper;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CategorieMapper
{
    public function __construct(
        private readonly UploaderHelper $uploaderHelper,
        private readonly UrlHelper $urlHelper,

    ) {}

    public function toDto(Categorie $categorie,ProduitMapper $produitMapper
): CategorieDto
    {
        $produits = [];

        foreach ($categorie->getProduit() as $item) {
            $produit = $produitMapper->toDto($item);
            $produits[] = $produit;
        }
        $path = $this->uploaderHelper->asset($categorie->getImage(), 'imageFile');
        $imageUrl = '';
        if ($path) {
           $imageUrl =  $this->urlHelper->getAbsoluteUrl($path);
        }
        return new CategorieDto(
            id: $categorie->getId(),
            name: $categorie->getName(),
            description: $categorie->getDescription(),
            imageUrl: $imageUrl,
            produits: $produits
        );
    }
}
