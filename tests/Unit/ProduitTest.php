<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Image;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $produit = new Produit();

        $this->assertNull($produit->getId());
        $this->assertNull($produit->getName());
        $this->assertNull($produit->getDescription());
        $this->assertTrue($produit->isActive());
        $this->assertTrue($produit->isStock());
        $this->assertCount(0, $produit->getImages());
    }

    public function testNameCanBeSetAndRead(): void
    {
        $produit = new Produit();
        $produit->setName('Chaussure');

        $this->assertSame('Chaussure', $produit->getName());
    }

    public function testDescriptionCanBeSetAndRead(): void
    {
        $produit = new Produit();
        $produit->setDescription('Description du produit');

        $this->assertSame('Description du produit', $produit->getDescription());
    }

    public function testDescriptionCanBeNull(): void
    {
        $produit = new Produit();
        $produit->setDescription(null);

        $this->assertNull($produit->getDescription());
    }

    public function testIsActiveCanBeSetAndRead(): void
    {
        $produit = new Produit();
        $produit->setIsActive(false);

        $this->assertFalse($produit->isActive());
    }

    public function testStockCanBeSetAndRead(): void
    {
        $produit = new Produit();
        $produit->setStock(false);

        $this->assertFalse($produit->isStock());
    }

    public function testAddImageAddsImageToCollection(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);

        $this->assertCount(1, $produit->getImages());
        $this->assertTrue($produit->getImages()->contains($image));
    }

    public function testAddImageSetsOwningSide(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);

        $this->assertSame($produit, $image->getProduit());
    }

    public function testAddSameImageTwiceDoesNotDuplicate(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);
        $produit->addImage($image);

        $this->assertCount(1, $produit->getImages());
    }

    public function testRemoveImageRemovesImageFromCollection(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);
        $produit->removeImage($image);

        $this->assertCount(0, $produit->getImages());
        $this->assertFalse($produit->getImages()->contains($image));
    }

    public function testRemoveImageUnsetsOwningSide(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);
        $produit->removeImage($image);

        $this->assertNull($image->getProduit());
    }

    public function testRemoveImageThatDoesNotExistDoesNothing(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->removeImage($image);

        $this->assertCount(0, $produit->getImages());
        $this->assertNull($image->getProduit());
    }
}