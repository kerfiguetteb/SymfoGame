<?php

namespace App\Tests\Unit;

// Import des classes nécessaires pour les tests
use App\Entity\Image;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

// Classe de test pour l'entité Produit (et Image)
class ProduitTest extends TestCase
{
    /**
     * Exo 1 : Test du setter et getter du nom du produit
     */
    public function testSetName(): void
    {
        // Arrange : création d’un produit et définition d’un nom
        $produit = new Produit();
        $nameProduit = 'produit ';

        // Act : on définit le nom
        $produit->setName($nameProduit);

        // Assert : on vérifie que le nom est bien enregistré
        self::assertSame($nameProduit, $produit->getName());
    }

    /**
     * Exo 2 : Test du champ booléen isActive
     */
    public function testIsActive(): void
    {
        // Création d’un produit
        $produit = new Produit();

        // On désactive le produit
        $produit->setIsActive(false);

        // Vérifie que isActive retourne bien false
        self::assertFalse($produit->isActive());
    }

    /**
     * Exo 3 : Test que la description peut être null
     */
    public function testDescriptionNullable(): void
    {
        $produit = new Produit();

        // On met la description à null
        $produit->setDescription(null);

        // Vérifie que la valeur est bien null
        self::assertNull($produit->getDescription());
    }

    /**
     * Exo 4 : Test du nom de fichier de l'image
     */
    public function testImageName(): void
    {
        $image = new Image();

        // Définition du nom de l'image
        $image->setImageName('photo.jpg');

        // Vérifie que le nom est correctement enregistré
        self::assertSame('photo.jpg', $image->getImageName());
    }

    /**
     * Exo 5 : Test de la relation entre Image et Produit
     */
    public function testImageProduitRelation(): void
    {
        $image = new Image();
        $produit = new Produit();

        // On associe un produit à l’image
        $image->setProduit($produit);

        // Vérifie que la relation est bien définie
        self::assertSame($produit, $image->getProduit());
    }

    /**
     * Exo 6 : Test que setImageFile met à jour la date updatedAt
     */
    public function testSetImageFileUpdatesDate(): void
    {
        $image = new Image();

        // Création d’un fichier temporaire simulant une image
        $tmpFile = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($tmpFile, 'fake');

        // Création d’un objet File Symfony
        $file = new File($tmpFile);

        // Vérifie qu’au départ updatedAt est null
        self::assertNull($image->getUpdatedAt());

        // On assigne un fichier à l’image
        $image->setImageFile($file);

        // Vérifie que le fichier est bien enregistré
        self::assertSame($file, $image->getImageFile());

        // Vérifie que la date updatedAt a été mise à jour automatiquement
        self::assertInstanceOf(\DateTimeImmutable::class, $image->getUpdatedAt());

        // Suppression du fichier temporaire pour éviter les fichiers inutiles
        unlink($tmpFile);
    }
}