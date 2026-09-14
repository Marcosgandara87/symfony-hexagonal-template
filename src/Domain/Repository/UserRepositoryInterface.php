<?php

namespace App\Domain\Repository;
use App\Domain\Model\User as DomainUser;

                           
interface UserRepositoryInterface
{

    public function findByEmail(string $email): ?DomainUser;
    public function saveNewUser(DomainUser $user): ?DomainUser;

}
