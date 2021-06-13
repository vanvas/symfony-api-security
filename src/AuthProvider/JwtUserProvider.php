<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\AuthProvider;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Vim\ApiSecurity\Exception\InvalidTokenException;
use Vim\ApiSecurity\Exception\NotSupportedException;
use Vim\ApiSecurity\Service\JwtUserService;
use Vim\ApiSecurity\User\JwtUser;
use Vim\ApiSecurity\User\JwtUserInterface;

class JwtUserProvider implements JwtUserProviderInterface
{
    public function __construct(private JwtUserService $jwtUserService)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        throw new NotSupportedException();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException();
    }

    /**
     * {@inheritdoc}
     */
    #[Pure]
    public function supportsClass($class): bool
    {
        return is_subclass_of($class, JwtUserInterface::class);
    }

    public function loadUserByToken(string $token): ?JwtUserInterface
    {
        try {
            $payload = $this->jwtUserService->extractPayloadFromToken($token);
        } catch (InvalidTokenException $exception) {
            return null;
        }

        return new JwtUser($token, $payload['roles']);
    }
}
