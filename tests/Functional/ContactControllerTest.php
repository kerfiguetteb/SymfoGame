<?php

namespace App\Tests\Functional;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    // Client HTTP Symfony utilisé pour simuler un navigateur
    private ?KernelBrowser $client = null;

    // EntityManager pour interagir avec la base de données
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Permet de s'assurer que le kernel est bien réinitialisé entre chaque test
        self::ensureKernelShutdown();

        // Création du client HTTP
        $this->client = static::createClient();

        // Récupération de l'EntityManager depuis le container Symfony
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyage de l'EntityManager pour éviter les effets entre les tests
        if ($this->entityManager !== null) {
            $this->entityManager->clear();  // vide le cache Doctrine
            $this->entityManager->close();  // ferme la connexion
            $this->entityManager = null;    // libère la mémoire
        }

        // Réinitialisation du client
        $this->client = null;
    }

    public function testReadAllContactsPageIsSuccessful(): void
    {
        // Appel de la page listant tous les contacts
        $this->client->request('GET', '/contact');

        // Vérifie que la réponse HTTP est correcte (code 200)
        self::assertResponseIsSuccessful();
    }

    public function testReadOneContactPageIsSuccessful(): void
    {
        // Création d'un contact en base de données
        $contact = new Contact();
        $contact->setNom('Durand');
        $contact->setPrenom('Paul');
        $contact->setEmail('paul.durand@example.com');
        $contact->setDescription("Lecture d'un contact");

        // Sauvegarde en base
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // Appel de la page de détail du contact
        $this->client->request('GET', '/contact/'.$contact->getId());

        // Vérifie que la page est accessible
        self::assertResponseIsSuccessful();
    }

    public function testSubmitContactForm(): void
    {
        // Accès à la page de création
        $crawler = $this->client->request('GET', '/contact/new');

        self::assertResponseIsSuccessful();

        // Remplissage du formulaire avec des données valides
        $form = $crawler->selectButton('Save')->form([
            'contact[nom]' => 'Leroy',
            'contact[prenom]' => 'Sophie',
            'contact[email]' => 'sophie.leroy@example.com',
            'contact[description]' => 'Bonjour, ceci est un test fonctionnel.',
        ]);

        // Soumission du formulaire
        $this->client->submit($form);

        // Vérifie qu'une redirection a lieu (formulaire valide)
        self::assertResponseRedirects();

        // Suit la redirection
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        // Vérifie que le contact est bien enregistré en base
        $contact = $this->entityManager
            ->getRepository(Contact::class)
            ->findOneBy(['email' => 'sophie.leroy@example.com']);

        self::assertNotNull($contact);

        // Vérifie que les données sont correctes
        self::assertSame('Leroy', $contact->getNom());
        self::assertSame('Sophie', $contact->getPrenom());
        self::assertSame('Bonjour, ceci est un test fonctionnel.', $contact->getDescription());
    }

    public function testSubmitInvalidContactFormWhenEmailIsEmpty(): void
    {
        // Accès à la page du formulaire
        $crawler = $this->client->request('GET', '/contact/new');

        self::assertResponseIsSuccessful();

        // Remplissage du formulaire avec un email vide
        $form = $crawler->selectButton('Save')->form([
            'contact[nom]' => 'test',
            'contact[prenom]' => 'Sophie',
            'contact[email]' => '',
            'contact[description]' => 'Message sans email',
        ]);

        // Soumission du formulaire
        $this->client->submit($form);

        // Le formulaire étant invalide, Symfony retourne un code 422
        self::assertResponseStatusCodeSame(422);

        // Vérifie qu'il n'y a pas de redirection
        self::assertResponseNotHasHeader('Location');

        // Vérifie que le formulaire est toujours affiché
        self::assertSelectorExists('form');

        // Vérifie qu'aucun contact n'a été enregistré en base
        $contact = $this->entityManager
            ->getRepository(Contact::class)
            ->findOneBy(['nom' => 'test']);

        self::assertNull($contact);
    }
}