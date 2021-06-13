<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\Service;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\User\UserInterface;
use Vim\ApiSecurity\Exception\InvalidTokenException;
use Psr\Log\LoggerInterface;

class JwtUserService
{
    public function __construct(
        private string $secretKey,
        private int $leeway,
        private string $exp,
        private LoggerInterface $logger
    )
    {
    }

    public function createJwtToken(UserInterface $user): string
    {
        try {
            $exp = new \DateTimeImmutable($this->exp);
        } catch (\Exception $exception) {
            throw new InvalidTokenException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $payload = [
            'sub' => $user->getUsername(),
            'exp' => $exp->getTimestamp(),
            'roles' => $user->getRoles(),
        ];

        return JWT::encode($payload, $this->secretKey);
    }

    public function extractPayloadFromToken(string $hash): array
    {
        JWT::$leeway = $this->leeway;

        try {
            $payload = (array)JWT::decode($hash, $this->secretKey, ['HS256']);
        } catch (BeforeValidException | ExpiredException $exception) {
            throw new InvalidTokenException();
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'error' => $exception,
            ]);

            throw new InvalidTokenException();
        }

        return $payload;
    }
}
