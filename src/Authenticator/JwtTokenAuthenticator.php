<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\Authenticator;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Vim\ApiSecurity\AuthProvider\JwtUserProviderInterface;
use Vim\ApiSecurity\Exception\InvalidTokenException;
use Vim\ApiSecurity\Exception\NotSupportedException;
use Vim\ApiSecurity\Service\JwtUserService;
use Vim\ApiSecurity\User\JwtUserInterface;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(private JwtUserService $jwtUserService)
    {
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse('Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization')
            || $request->server->has('Authorization');
    }

    /**
     * @inheritdoc
     */
    #[ArrayShape(['token' => "mixed|null"])]
    public function getCredentials(Request $request)
    {
        preg_match('/^Bearer\s(?<token>.*)$/', $request->headers->get('Authorization') ?? $request->server->get('Authorization'), $matches);

        return [
            'token' => $matches['token'] ?? null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?JwtUserInterface
    {
        if (!$userProvider instanceof JwtUserProviderInterface) {
            throw new NotSupportedException();
        }

        if (!$credentials || empty($credentials['token'])) {
            return null;
        }

        return $userProvider->loadUserByToken($credentials['token']);
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface|JwtUserInterface $user): bool
    {
        try {
            $this->jwtUserService->extractPayloadFromToken($user->getToken());

            return true;
        } catch (InvalidTokenException $exception) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse('Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
