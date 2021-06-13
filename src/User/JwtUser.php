<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\User;

class JwtUser implements JwtUserInterface
{
    public function __construct(private string $token, private array $roles = [])
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
    }

    public function eraseCredentials()
    {
    }
}
