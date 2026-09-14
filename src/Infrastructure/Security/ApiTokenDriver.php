<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use App\Domain\Security\TokenDriverInterface;
use App\Domain\Security\TokenType;
use Symfony\Contracts\Cache\CacheInterface;

class ApiTokenDriver implements TokenDriverInterface
{
    private CacheInterface $cacheApiTokens;
    public function __construct(CacheInterface $cacheApiTokens)
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
        if (strlen($token) === 64 && !str_contains($token, '.')) {
            return $this->cacheApiTokens->get($token, function () {
                return null;
            });
        }
        return null;
    }

}
