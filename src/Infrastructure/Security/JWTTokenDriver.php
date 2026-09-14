<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use App\Domain\Security\TokenDriverInterface;
use App\Domain\Security\TokenType;
use App\Infrastructure\Entity\UserInfrastructure;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // 🚀 Importamos el componente oficial

class JWTTokenDriver implements TokenDriverInterface
{
    private JWTTokenManagerInterface $jwtManager;

    // 🚀 Inyectamos el Manager que lee tus llaves .pem automáticamente
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * 1. FABRICAR EL JWT (Para el Login)
     */
    public function generate(User $user, int $ttlInSeconds): string
    {
       $userEntity = new UserInfrastructure();
       $userEntity->setEmail($user->getEmail());
       $userEntity->setPassword($user->getPassword());
        return $this->jwtManager->create($userEntity); 
    }

    public function supports(TokenType $type): bool
    {
        return $type === TokenType::JWT;
    }

    /**
     * 2. DECODIFICAR EL JWT (Para tu AccessTokenAuthenticator)
     * Te devuelve un array con los datos de adentro (email, exp, etc.) o null si la firma es falsa [2.2]
     */
    public function validate(string $token): null|string
    {
        try {
            // El método parse() lee config/jwt/public.pem y valida matemáticamente la firma en el aire [2.2]
            $decode=  $this->jwtManager->parse($token);
            return $decode['username'] ?? $decode['email'] ?? null;
        } catch (\Exception $e) {
            // Si el token está alterado, expiró o la firma no coincide, escupe null [2.2]
            return null; 
        }
    }
}
