<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Utilisateurs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct( 
            private readonly UserPasswordHasherInterface $hasher
        ){

    }   

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        //id, login, password, roles, email, is_verified
        $user = new Utilisateurs();
        $user->setLogin("game_administrator")
             ->setRoles(['ROLE_ADMIN'])
             ->setEmail("game_administrator@monjeu.fr")
             ->setVerified(true)
             ->setPassword($this->hasher->hashPassword($user, "motDePasseAChanger"));
        $manager->persist($user);

        $user = new Utilisateurs();
        $user->setLogin("thierry")
             ->setRoles(['ROLE_ADMIN'])
             ->setEmail("thierry@monjeu.fr")
             ->setVerified(true)
             ->setPassword($this->hasher->hashPassword($user, "motDePasseAChanger"));
        $manager->persist($user);

        $user = new Utilisateurs();
        $user->setLogin("louise")
             ->setRoles(['ROLE_ADMIN'])
             ->setEmail("louise@monjeu.fr")
             ->setVerified(true)
             ->setPassword($this->hasher->hashPassword($user, "motDePasseAChanger"));
        $manager->persist($user);

        $user = new Utilisateurs();
        $user->setLogin("elodie")
             ->setRoles(['ROLE_ADMIN'])
             ->setEmail("elodie@monjeu.fr")
             ->setVerified(true)
             ->setPassword($this->hasher->hashPassword($user, "motDePasseAChanger"));
        $manager->persist($user);

        $manager->flush();
    }
}
