<?php

namespace App\Tests\Integration;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContactDoctrineTest extends KernelTestCase
{
    // EntityManager utilisé pour interagir avec la base de données
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        // Démarrage du kernel Symfony
        self::bootKernel();

        // Récupération de l'EntityManager depuis le container
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyage de l'EntityManager après chaque test
        // Permet d'éviter les effets de bord entre les tests
        if ($this->entityManager !== null) {
            $this->entityManager->clear();  // vide le cache Doctrine
            $this->entityManager->close();  // ferme la connexion
            $this->entityManager = null;    // libère la mémoire
        }
    }

    public function testPersistContact(): void
    {
        // Création d'une entité Contact
        $contact = new Contact();
        $contact->setNom('Dupont');
        $contact->setPrenom('Jean');
        $contact->setEmail('jean.dupont@example.com');
        $contact->setDescription('Message de test');

        // Persistance de l'entité en base de données
        $this->entityManager->persist($contact);

        // Exécution de la requête SQL (INSERT)
        $this->entityManager->flush();

        // Vérifie que l'entité a bien été enregistrée
        // L'id est généré automatiquement par la base
        self::assertNotNull($contact->getId());
    }

    public function testFindContactById(): void
    {
        // Création d'un contact
        $contact = new Contact();
        $contact->setNom('Martin');
        $contact->setPrenom('Julie');
        $contact->setEmail('julie.martin@example.com');
        $contact->setDescription('Autre message');

        // Sauvegarde en base
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // Récupération de l'identifiant
        $contactId = $contact->getId();

        // Vidage de l'EntityManager
        // Permet de simuler un nouveau chargement depuis la base
        // et non depuis le cache Doctrine
        $this->entityManager->clear();

        // Recherche du contact en base via le repository
        $found = $this->entityManager
            ->getRepository(Contact::class)
            ->find($contactId);

        // Vérifie que l'objet récupéré est bien une instance de Contact
        self::assertInstanceOf(Contact::class, $found);

        // Vérifie que les données récupérées sont correctes
        self::assertSame('Martin', $found->getNom());
        self::assertSame('Julie', $found->getPrenom());
        self::assertSame('julie.martin@example.com', $found->getEmail());
        self::assertSame('Autre message', $found->getDescription());
    }
}