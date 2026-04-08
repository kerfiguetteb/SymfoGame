<?php

namespace App\Controller\Api;

use App\Entity\Produit;
use App\Mapper\ProduitMapper;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/produits')]
class ProduitController extends AbstractController
{

    #[Route('', name: 'list_produit', methods: ['GET'])]
    public function index(ProduitMapper $mapper, ProduitRepository $pr): JsonResponse
    {
        $produits = $pr->findAllWithImages();
        $data = array_map(
            fn(Produit $item) => $mapper->toDto($item),
            $produits
        );
        return $this->json($data, Response::HTTP_OK);
    }
}
