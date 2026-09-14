<?php

namespace App\Domain\Security;

use App\Domain\Model\User;

interface PasswordHasherInterface
{
    public function isValid(User $user, string $plainPassword): bool;
    public function hash(User $user, string $plainPassword): bool;
}
