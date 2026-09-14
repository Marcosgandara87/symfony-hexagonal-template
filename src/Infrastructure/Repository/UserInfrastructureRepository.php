<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\User as DomainUser;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Entity\UserInfrastructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserInfrastructureRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInfrastructure::class);
    }

    public function findByEmail(string $email): ?DomainUser
    {
        /** @var \App\Infrastructure\Entity\UserInfrastructure|null $userEntity */
        $userEntity = $this->findOneBy(['email' => $email]);

        if (null === $userEntity) {
            return null;
        }

        return new DomainUser(
            $userEntity->getEmail(),
            $userEntity->getPassword(),
            $userEntity->getTokenType()
        );
    }

    public function saveNewUser(DomainUser $user): ?DomainUser
    {
        $useInfrastructure = $this->findOneBy(['email' => $user->getEmail()]);

        if (null != $useInfrastructure) {
            throw new \RuntimeException('El usuario ya existe.');
        }

        $useInfrastructure = new UserInfrastructure();
        $useInfrastructure->setEmail($user->getEmail());
        $useInfrastructure->setPassword($user->getPassword());
        $entityManager = $this->getEntityManager();
        $entityManager->persist($useInfrastructure);
        $entityManager->flush();
        return $user;

    }

}
