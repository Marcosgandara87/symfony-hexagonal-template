<?php

namespace App\Application;

use App\Domain\Security\PasswordHasherInterface;
use App\Domain\Security\TokenStorageInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Security\TokenFactoryInterface;
use RuntimeException;


class LoginUseCase
{

    private UserRepositoryInterface $userRepo;
    private PasswordHasherInterface $passwordHasher;
    private TokenStorageInterface $tokenStorage;
    private TokenFactoryInterface $tokenFactory;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, PasswordHasherInterface $passwordHasherInterface, TokenStorageInterface $tokenStorageInterface, TokenFactoryInterface $tokenFactoryInterface)
    {
        $this->userRepo = $userRepositoryInterface;
        $this->passwordHasher = $passwordHasherInterface;
        $this->tokenStorage = $tokenStorageInterface;
        $this->tokenFactory = $tokenFactoryInterface;
    }


    public function execute(string $email, string $password)
    {

        $user = $this->userRepo->findByEmail($email);

        if (null === $user) {
            throw new RuntimeException('Credenciales inválidas.');
        }

        if (!$this->passwordHasher->isValid($user, $password)) {
            throw new RuntimeException('Credenciales inválidas.');
        }

        $token = $this->tokenFactory->createToken($user, 80000);
        $this->tokenStorage->save($token, $user, 86400);

        return $token;
    }
}
