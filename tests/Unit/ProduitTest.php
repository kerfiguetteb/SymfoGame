<?php

namespace App\Tests\Unit;

use App\Entity\Image;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

final class ProduitTest extends TestCase
{
    public function testAddImageSetsBothSidesOfRelation(): void
    {
        $produit = new Produit();
        $image = new Image();

        $produit->addImage($image);

        self::assertCount(1, $produit->getImages());

        self::assertSame($produit, $image->getProduit());
    }

    public function testAddImageDoesNotDuplicateSameImage(): void
    {
        $produit = new Produit();
        $image = new Image();
        $produit->addImage($image);
        $produit->addImage($image);
        self::assertCount(1, $produit->getImages());
    }

    public function testRemoveImageUnsetsInverseRelation(): void
    {
        $produit = new Produit();
        $image = new Image();
        $produit->addImage($image);
        $produit->removeImage($image);
        self::assertCount(0, $produit->getImages());
        self::assertNull($image->getProduit());
    }
}