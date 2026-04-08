<?php

namespace App\Tests\Unit;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    public function testNomGetterSetter(): void
    {
        // Création d'une instance de l'entité Contact
        $contact = new Contact();

        // Définition de la propriété "nom"
        $contact->setNom('Dupont');

        // Vérifie que la valeur est correctement stockée
        // et restituée par le getter
        self::assertSame('Dupont', $contact->getNom());
    }

    public function testPrenomGetterSetter(): void
    {
        // Création d'une instance de l'entité
        $contact = new Contact();

        // Définition de la propriété "prenom"
        $contact->setPrenom('Jean');

        // Vérification du bon fonctionnement du getter
        self::assertSame('Jean', $contact->getPrenom());
    }

    public function testEmailGetterSetter(): void
    {
        // Création d'une instance de l'entité
        $contact = new Contact();

        // Définition de la propriété "email"
        $contact->setEmail('jean.dupont@example.com');

        // Vérifie que l'email est correctement enregistré et retourné
        self::assertSame('jean.dupont@example.com', $contact->getEmail());
    }

    public function testCommentaireGetterSetter(): void
    {
        // Création d'une instance de l'entité
        $contact = new Contact();

        // Définition de la propriété "description" (commentaire)
        $contact->setDescription('Bonjour, ceci est un message.');

        // Vérifie que la valeur est correctement stockée
        self::assertSame('Bonjour, ceci est un message.', $contact->getDescription());
    }
}