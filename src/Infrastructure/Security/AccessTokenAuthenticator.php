<?php

namespace App\Infrastructure\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    private CacheInterface $cacheApiTokens;
    private iterable $drivers;
    public function __construct(
        CacheInterface $cacheApiTokens,
        #[AutowireIterator('app.token_driver')]
        iterable $drivers
    ) {
        $this->cacheApiTokens = $cacheApiTokens;

        $this->drivers = $drivers;
    }

    /**
     * Determina si este autenticador debe ejecutarse para la petición actual.
     */
    public function supports(Request $request): ?bool
    {
        // Se ejecuta para cualquier ruta /api excepto la de login
        return str_starts_with($request->getPathInfo(), '/api/v1');
    }

    /**
     * Extrae el token de la cabecera HTTP y lo valida contra Redis.
     */
    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (null === $authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('No se proporcionó un token de acceso (Bearer token).');
        }

        // Extraemos el string puro del token (sacando los primeros 7 caracteres de "Bearer ")
        $token = substr($authorizationHeader, 7);
        $userEmail= null;
        /** @var \App\Domain\Security\TokenDriverInterface $driver */
        foreach ($this->drivers as $driver) {
            if($userEmail = $driver->validate($token)) break;
        }

        if (null === $userEmail) {
            throw new CustomUserMessageAuthenticationException('El token proporcionado no existe o ya caducó.');
        }

        // Si el token existe en Redis, creamos un pasaporte seguro con el email del usuario
        return new SelfValidatingPassport(new UserBadge($userEmail));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Retornar null permite que la petición siga su curso hacia el Controlador
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'error' => 'No autorizado',
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
