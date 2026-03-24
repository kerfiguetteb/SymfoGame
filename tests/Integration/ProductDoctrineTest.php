<?php

namespace App\Tests\Integration;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductDoctrineTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();

        $em = static::getContainer()->get('doctrine')->getManager();

        $produit = new Produit();
        $produit->setName("Produit de test");
        $produit->setDescription("Produit de test description");

        $categorie = new Categorie();
        $categorie->setName('categorie de test');

        $produit->setCategorie($categorie);

        $em->persist($produit);
        $em->flush();

        $this->assertNotNull($produit->getId());

        }
}
