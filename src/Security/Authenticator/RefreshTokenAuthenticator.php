<?php

namespace App\Security\Authenticator;

use App\Constant\Security\AuthenticationConstant;
use App\Security\Provider\RefreshTokenProvider;
use Gesdinet\JWTRefreshTokenBundle\Request\RequestRefreshToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * Class RefreshTokenAuthenticator
 * @package App\Security\Authenticator
 */
class RefreshTokenAuthenticator implements AuthenticationFailureHandlerInterface, SimplePreAuthenticatorInterface
{
    /**
     * @param Request $request
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey): PreAuthenticatedToken
    {
        $refreshTokenString = RequestRefreshToken::getRefreshToken($request);

        return new PreAuthenticatedToken(
            '',
            $refreshTokenString,
            $providerKey
        );
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @throws \InvalidArgumentException
     * @throws AuthenticationException
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(
        TokenInterface $token,
        UserProviderInterface $userProvider,
        $providerKey
    ): PreAuthenticatedToken {
        if ( ! $userProvider instanceof RefreshTokenProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of RefreshTokenProvider (%s was given).',
                    \get_class($userProvider)
                )
            );
        }

        $refreshToken = $token->getCredentials();
        $username = $userProvider->getUsernameForRefreshToken($refreshToken);

        if ( ! $username) {
            throw new AuthenticationException(
                AuthenticationConstant::JWT_REFRESH_FAILED
            );
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $refreshToken,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @throws \InvalidArgumentException
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response('Refresh token authentication failed.', 403);
    }
}
