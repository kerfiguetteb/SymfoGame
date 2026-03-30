<?php

namespace App\Tests\Functional;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProduitControllerTest extends WebTestCase
{
    /**
     * Client HTTP Symfony.
     * Il simule un navigateur pour faire des requêtes GET/POST.
     */
    private ?KernelBrowser $client = null;

    /**
     * EntityManager Doctrine pour accéder à la base de données de test.
     */
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        // On s'assure de repartir d'un kernel propre à chaque test.
        self::ensureKernelShutdown();

        // Création du client HTTP.
        $this->client = static::createClient();

        // Récupération de l'EntityManager depuis le container Symfony.
        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        // Nettoyage de l'EntityManager après chaque test.
        if ($this->entityManager !== null) {
            $this->entityManager->clear();
        }

        $this->entityManager = null;
        $this->client = null;

    }

    /**
     * Connecte l'utilisateur utilisé pour les tests fonctionnels.
     *
     * Ici on prend l'utilisateur avec l'ID 1.
     * Idéalement, en projet réel, on utiliserait des fixtures dédiées.
     */
    private function loginTestUser(): User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(1);

        // Vérifie qu'un utilisateur existe bien en base de test.
        self::assertNotNull($user, 'Aucun utilisateur avec l\'ID 1 n\'a été trouvé en base de test.');

        // Connexion de l'utilisateur sur le client HTTP.
        $this->client->loginUser($user);

        return $user;
    }

    /**
     * Test : la page de liste des produits est accessible.
     */
    public function testProduitIndexPageIsSuccessful(): void
    {
        $this->loginTestUser();

        // Requête HTTP GET sur la page liste.
        $this->client->request('GET', '/produit');

        // Vérifie que la réponse HTTP est bien un succès (200-299).
        self::assertResponseIsSuccessful();
    }

    /**
     * Test : la page de création d'un produit est accessible.
     */
    public function testProduitNewPageIsSuccessful(): void
    {
        $this->loginTestUser();

        // Requête HTTP GET sur la page de création.
        $this->client->request('GET', '/produit/new');

        // Vérifie que la page répond correctement.
        self::assertResponseIsSuccessful();
    }

    /**
     * Test : création d'un produit via le formulaire.
     */
    public function testCreateProduitForm(): void
    {
        $this->loginTestUser();

        // Chargement de la page du formulaire.
        $crawler = $this->client->request('GET', '/produit/new');

        // Vérifie que la page s'affiche correctement.
        self::assertResponseIsSuccessful();

        // Sélection du formulaire via le bouton "Save".
        // Le texte doit correspondre exactement au libellé du bouton dans le template Twig.
        $form = $crawler->selectButton('Save')->form([
            'produit[name]' => 'Produit fonctionnel',
            'produit[description]' => 'Description fonctionnelle',
            'produit[stock]' => 1,
        ]);

        // Soumission du formulaire.
        $this->client->submit($form);

        // Après création, on attend généralement une redirection.
        self::assertResponseRedirects();

        // On suit la redirection.
        $this->client->followRedirect();

        // Vérifie que la page finale est bien accessible.
        self::assertResponseIsSuccessful();

        // Vérifie en base que le produit a bien été créé.
        $produit = $this->entityManager
            ->getRepository(Produit::class)
            ->findOneBy(['name' => 'Produit fonctionnel']);

        self::assertNotNull($produit, 'Le produit n\'a pas été enregistré en base.');

        // Vérification des données persistées.
        self::assertSame('Description fonctionnelle', $produit->getDescription());
        self::assertSame(true, $produit->isStock());

    }
}