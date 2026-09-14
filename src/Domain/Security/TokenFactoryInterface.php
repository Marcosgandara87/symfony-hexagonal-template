<?php

namespace App\Domain\Security;

use App\Domain\Model\User;

interface TokenFactoryInterface
{
    public function createToken(User $user, int $ttlInSeconds): string;
}
