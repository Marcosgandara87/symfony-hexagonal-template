<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User as DomainUser;
use App\Domain\Security\TokenStorageInterface;
use App\Domain\Security\TokenType;
use App\Infrastructure\Entity\AccessTokenInfrastructure;
use App\Infrastructure\Entity\UserInfrastructure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RedisTokenStorage implements TokenStorageInterface
{

    private EntityManagerInterface $entityManager;
    private CacheInterface $cacheApiTokens;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cacheApiTokens)
    {
        $this->entityManager = $entityManager;
        $this->cacheApiTokens = $cacheApiTokens;
    }

    public function save(string $token, DomainUser $user, int $ttlInSeconds): void
    {   
        if($user->getTokenType() == TokenType::JWT) return;

        /** @var \App\Infrastructure\Entity\UserInfrastructure|null $userEntity */
        $userEntity = $this->entityManager->getRepository(UserInfrastructure::class)
            ->findOneBy(['email' => $user->getEmail()]);

        if (null === $userEntity) {
            throw new \RuntimeException('No se encontró el usuario en la base de datos de infraestructura.');
        }

        $expiresAt = (new \DateTimeImmutable())->modify(sprintf('+%d seconds', $ttlInSeconds));

        $accessTokenEntity = new AccessTokenInfrastructure();
        $accessTokenEntity->setToken($token);
        $accessTokenEntity->setExpiresAt($expiresAt);
        $accessTokenEntity->setApiUser($userEntity);

        $this->entityManager->persist($accessTokenEntity);
        $this->entityManager->flush();

        $this->cacheApiTokens->get($token, function (ItemInterface $item) use ($user) {
            return $user->getEmail();
        });
    }
}
