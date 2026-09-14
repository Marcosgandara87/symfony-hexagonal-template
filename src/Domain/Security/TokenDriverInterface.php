<?php

namespace App\Domain\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

use App\Domain\Model\User;
#[AutoconfigureTag('app.token_driver')]
interface TokenDriverInterface
{
    public function generate(User $user, int $ttlInSeconds): string;
    public function supports(TokenType $type): bool; 
    public function validate(string $token): null|string;
}
