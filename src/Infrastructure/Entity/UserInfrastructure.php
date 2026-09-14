<?php

namespace App\Infrastructure\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Security\TokenType;
use PhpParser\Token;

#[ORM\Entity]
#[ORM\Table(name: 'api_users')]
class UserInfrastructure implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;
    
    #[ORM\Column(length: 20, type: 'string', enumType: TokenType::class)]
    private TokenType $tokenType = TokenType::JWT;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    // 🚀 MÉTODOS OBLIGATORIOS EXIGIDOS POR USERINTERFACE
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // No almacenamos credenciales temporales en texto plano, queda vacío.
    }


    public function setTokenType(TokenType $type): static
    {
        $this->tokenType = $type;
        return $this;
    }

    public function getTokenType(): ?TokenType
    {
        return $this->tokenType;
    }
}
