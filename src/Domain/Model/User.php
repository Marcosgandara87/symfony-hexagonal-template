<?php
namespace App\Domain\Model;
use App\Domain\Security\TokenType;

class User {
    private string $email;
    private string $password;
    private TokenType $tokenType;

    public function __construct(string $email,string $password, TokenType $tokenType)
    {
        $this->email= $email;
        $this->password= $password;
        $this->tokenType= $tokenType;
    }

    public function getEmail(): string 
    {
        return $this->email;
    }

    public function getPassword(): string 
    {
        return $this->password;
    }
    public function getTokenType(): TokenType
    {
        return $this->tokenType;
    }
}