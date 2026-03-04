<?php

namespace App\Controller\Api;

use App\Entity\Produit;
use App\Mapper\ProduitMapper;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/produits')]
class ProduitController extends AbstractController
{
    #[Route('', name: 'liste_produit', methods:['GET'])]
    public function index(ProduitRepository $pr, ProduitMapper $produitMapper): JsonResponse
    {
        $produits = $pr->findAllWithImages();
        $data = array_map(
            fn(Produit $produit) => $produitMapper->toDto($produit), $produits
        );
        return $this->json($data);
    }

    #[Route('/{id}', name: 'produit', methods:['GET'])]
    public function show(Produit $produit, ProduitMapper $produitMapper): JsonResponse
    {
        $data =  $produitMapper->toDto($produit);
        return $this->json($data);
    }
}
