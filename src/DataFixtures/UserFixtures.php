<?php

namespace App\DataFixtures;

use App\Domain\Security\TokenType;
use App\Infrastructure\Entity\UserInfrastructure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Creamos el usuario de prueba
        $user = new UserInfrastructure();
        $user->setEmail('proveedor@bookingkit.com');

        // 2. Hasheamos la contraseña de forma segura usando el componente de Symfony
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setTokenType(TokenType::API);
        $manager->persist($user);
        $manager->flush();


         // 1. Creamos el usuario de prueba
        $user = new UserInfrastructure();
        $user->setEmail('proveedor1@bookingkit.com');

        // 2. Hasheamos la contraseña de forma segura usando el componente de Symfony
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setTokenType(TokenType::JWT);
        $manager->persist($user);
        $manager->flush();
    }
}
