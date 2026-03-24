<?php

namespace App\Controller\Api;

use App\Entity\Categorie;
use App\Mapper\CategorieMapper;
use App\Mapper\ProduitMapper;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/categories')]
class CategorieController extends AbstractController
{

    #[Route('', name: 'api_categorie_index', methods:['GET'])]
    public function index(
        CategorieRepository $categorieRepository, 
        CategorieMapper $categorieMapper, 
        ProduitMapper $produitMapper
        ): JsonResponse
    {
        $categories = $categorieRepository->findAll();
        $data = array_map(
            fn(Categorie $item) => $categorieMapper->toDto($item, $produitMapper), $categories
        );
        return $this->json($data);


    }
}