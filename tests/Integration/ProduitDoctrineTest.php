<?php

namespace App\Tests\Integration;

use App\Entity\Image;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProduitDoctrineTest extends KernelTestCase
{
    // EntityManager Doctrine utilisé pour interagir avec la base de données
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Méthode exécutée avant chaque test.
     * Elle démarre le kernel Symfony et récupère l'EntityManager.
     */
    protected function setUp(): void
    {
        // Démarre l'application Symfony pour avoir accès au container
        self::bootKernel();

        // Récupère Doctrine puis l'EntityManager principal
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Méthode exécutée après chaque test.
     * Elle nettoie l'EntityManager pour éviter qu'un test influence le suivant.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager !== null) {
            // Détache toutes les entités gérées par Doctrine
            $this->entityManager->clear();

            // Ferme l'EntityManager courant
            $this->entityManager->close();

            // Supprime la référence PHP
            $this->entityManager = null;
        }
    }

    /**
     * Teste la persistance simple d'un produit en base.
     */
    public function testPersistProduit(): void
    {
        // Création d'un produit
        $produit = new Produit();
        $produit->setName('Produit integration');
        $produit->setDescription('Description integration');
        $produit->setIsActive(true);
        $produit->setStock(true);

        // Demande à Doctrine de persister l'entité
        $this->entityManager->persist($produit);

        // Exécute réellement la requête SQL en base
        $this->entityManager->flush();

        // Si l'ID existe, cela signifie que l'entité a bien été enregistrée
        self::assertNotNull($produit->getId());
    }

    /**
     * Teste la persistance d'un produit avec une image liée.
     * Ce test vérifie aussi le cascade persist sur la relation.
     */
    public function testPersistProduitWithImage(): void
    {
        // Création du produit
        $produit = new Produit();
        $produit->setName('Produit avec image');

        // Création de l'image liée au produit
        $image = new Image();
        $image->setImageName('test.jpg');

        // Ajoute l'image au produit et met à jour la relation bidirectionnelle
        $produit->addImage($image);

        // On persiste seulement le produit
        // Grâce à cascade persist, l'image sera persistée aussi
        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        // Vérifie que le produit a bien été enregistré
        self::assertNotNull($produit->getId());

        // Vérifie que l'image a aussi été enregistrée
        self::assertNotNull($image->getId());

        // Vérifie que la relation côté Image est correcte
        self::assertSame($produit, $image->getProduit());
    }

    /**
     * Teste la récupération d'un produit via son identifiant.
     */
    public function testFindProduitById(): void
    {
        // Création et enregistrement du produit
        $produit = new Produit();
        $produit->setName('Produit A');

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        // On récupère l'ID pour faire une recherche ensuite
        $produitId = $produit->getId();

        // Vide le cache interne de Doctrine pour forcer une vraie lecture en base
        $this->entityManager->clear();

        // Recherche du produit via le repository
        $found = $this->entityManager
            ->getRepository(Produit::class)
            ->find($produitId);

        // Vérifie que l'objet récupéré est bien un Produit
        self::assertInstanceOf(Produit::class, $found);

        // Vérifie que les données correspondent
        self::assertSame('Produit A', $found->getName());
    }

    /**
     * Teste la récupération d'un produit avec sa collection d'images.
     */
    public function testFindProduitWithImages(): void
    {
        // Création du produit
        $produit = new Produit();
        $produit->setName('Produit B');

        // Création d'une image liée
        $image = new Image();
        $image->setImageName('image-b.jpg');

        // Association image -> produit
        $produit->addImage($image);

        // Enregistrement en base
        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        // Sauvegarde de l'identifiant du produit
        $produitId = $produit->getId();

        // Nettoie le cache Doctrine pour relire les données depuis la base
        $this->entityManager->clear();

        // Récupération du produit depuis le repository
        $found = $this->entityManager
            ->getRepository(Produit::class)
            ->find($produitId);

        // Vérifie qu'on a bien récupéré un Produit
        self::assertInstanceOf(Produit::class, $found);

        // Vérifie qu'il possède bien une image
        self::assertCount(1, $found->getImages());

        // Vérifie le nom du produit
        self::assertSame('Produit B', $found->getName());

        // Récupère la première image de la collection
        $foundImage = $found->getImages()->first();

        // Vérifie le nom de l'image
        self::assertSame('image-b.jpg', $foundImage->getImageName());
    }
}