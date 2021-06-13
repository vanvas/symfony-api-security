<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface JwtUserInterface extends UserInterface
{
    public function getToken(): string;
}
