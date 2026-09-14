<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User as DomainUser;
use App\Domain\Security\PasswordHasherInterface;
use App\Infrastructure\Entity\UserInfrastructure;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SymfonyPasswordHasher implements PasswordHasherInterface
{
    private UserPasswordHasherInterface $symfonyHasher;

    public function __construct(UserPasswordHasherInterface $symfonyHasher)
    {
        $this->symfonyHasher = $symfonyHasher;
    }

    public function isValid(DomainUser $user, string $plainPassword): bool
    {
        $entity = new UserInfrastructure();
        $entity->setPassword($user->getPassword());

        return $this->symfonyHasher->isPasswordValid($entity, $plainPassword);
    }   

     public function hash(DomainUser $user, string $plainPassword): bool
    {
        $entity = new UserInfrastructure();
        $entity->setPassword($user->getPassword());

        return $this->symfonyHasher->hashPassword($entity, $plainPassword);
    }


}
