<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\AuthProvider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vim\ApiSecurity\User\JwtUserInterface;

interface JwtUserProviderInterface extends UserProviderInterface
{
    public function loadUserByToken(string $token): ?JwtUserInterface;
}
