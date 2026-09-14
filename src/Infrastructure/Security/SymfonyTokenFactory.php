<?php

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use App\Domain\Security\TokenFactoryInterface;
use App\Domain\Security\TokenDriverInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class SymfonyTokenFactory implements TokenFactoryInterface
{
    private iterable $drivers;

    // Symfony inyecta automáticamente todos los que implementen TokenDriverInterface
    public function __construct(
        #[AutowireIterator('app.token_driver')] 
        iterable $drivers
    ) {
        $this->drivers = $drivers;
    }

    public function createToken(User $user, int $ttlInSeconds): string
    {
        $userTokenType = $user->getTokenType();
        /** @var \App\Domain\Security\TokenDriverInterface $driver */
        foreach ($this->drivers as $driver) {
            if ($driver->supports($userTokenType)) {
                return $driver->generate($user, $ttlInSeconds);
            }
        }

        throw new RuntimeException(sprintf('No se encontró un generador para el tipo de token asignado al usuario.'));
    }
}
