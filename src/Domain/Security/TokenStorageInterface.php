<?php

namespace App\Domain\Security;

use App\Domain\Model\User;

interface TokenStorageInterface
{
    public function save(string $token, User $user, int $ttlInSeconds): void;
}
