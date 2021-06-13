<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\Authenticator;

use JetBrains\PhpStorm\ArrayShape;
use Vim\ApiSecurity\Service\JwtUserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtLoginAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(
        private string $routeName,
        private UserPasswordEncoderInterface $passwordEncoder,
        private JwtUserService $jwtUserService
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse('Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $this->routeName === $request->attributes->get('_route');
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['username' => "mixed|null", 'password' => "mixed|null"])]
    public function getCredentials(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        return [
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (!$credentials['username']) {
            return null;
        }

        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$credentials['password']) {
            return false;
        }

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse('Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): JsonResponse
    {
        $jwtToken = $this->jwtUserService->createJwtToken($token->getUser());
        $payload = $this->jwtUserService->extractPayloadFromToken($jwtToken);

        return new JsonResponse([
            'data' => [
                'accessToken' => [
                    'value' => $jwtToken,
                    'exp' => $payload['exp'],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
