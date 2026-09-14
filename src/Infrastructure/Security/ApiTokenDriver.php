<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use App\Domain\Security\TokenDriverInterface;
use App\Domain\Security\TokenType;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class ApiTokenDriver implements TokenDriverInterface
{
    #[Autowire(service: 'cache.api_tokens')]
    private CacheItemPoolInterface  $cacheApiTokens;
    public function __construct(CacheItemPoolInterface  $cacheApiTokens)
    {
        $this->cacheApiTokens = $cacheApiTokens;
    }

    public function generate(User $user, int $ttlInSeconds): string
    {
        return bin2hex(random_bytes(32));
    }

    public function supports(TokenType $type): bool
    {
        return $type === TokenType::API;
    }

    public function validate(string $token): null|string
    {
         if (strlen($token) !== 64 || str_contains($token, '.')) {
            return null;
        }
        $item = $this->cacheApiTokens->getItem($token);
        if (!$item->isHit()) {
            return null;
        }
        $email = $item->get();
        return \is_string($email) && $email !== '' ? $email : null;
    }

}
