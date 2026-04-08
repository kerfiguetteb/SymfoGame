<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 10; $i++) {
            $product = new Produit();
            $product->setName("Produit $i");
            $product->setDescription("description du produit: $i");
            $manager->persist($product);
        }

        $user = new User();
        $user->setEmail("admin@example.fr");
        $user->setRoles(["ROLE_ADMIN"]);
        $plainPassword = "admin";

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);
        $manager->persist($user);


        $manager->flush();
    }
}
