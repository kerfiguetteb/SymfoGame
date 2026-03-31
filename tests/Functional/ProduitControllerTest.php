<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{

    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/produits/new');

        $form = $crawler->selectButton('Save')->form([
            'produit[name]' => 'Mon Produit',
            'produit[categorie]' => 1
            
        ]);

        $client->submit($form);
      self::assertSelectorExists('.form-error-message');

        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h1', 'Produit index');

        
    }
}
